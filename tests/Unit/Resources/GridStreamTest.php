<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\NoSeekStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;
use Xingo\IDServer\Client\Middleware\PrepareResponseBody;
use Xingo\IDServer\Exceptions\ServerException;
use Xingo\IDServer\Exceptions\ValidationException;
use Xingo\IDServer\Manager;
use Xingo\IDServer\ServiceProvider;

class GridStreamTest extends TestCase
{
    private $application;
    private $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->application = new Application();
        $this->application->instance('env', 'testing');
        $this->application->instance('config', new Repository([
            'app' => ['locale' => 'en'],
            'idserver' => [
                'url' => 'https://idserver.test/',
                'classes' => [],
                'store' => [
                    'web' => ['client_id' => 'test', 'secret_key' => 'test'],
                    'cli' => ['client_id' => 'test', 'secret_key' => 'test'],
                ],
            ],
        ]));
        $this->application->instance('session', new Store('test', new ArraySessionHandler(120)));
        $this->application->instance('events', new Dispatcher($this->application));
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->application);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);
        parent::tearDown();
    }

    public function test_relays_csv_without_reading_it_before_the_callback()
    {
        $csv = "Id;Name\r\n" . str_repeat("1;été,semicolon;quote\"\r\n", 200);
        $response = $this->streamedResponse(200, $csv, ['Content-Type' => 'text/csv']);
        $body = $response->getBody();
        $options = [];
        $manager = $this->manager([$response], $options);

        $callback = $manager->grids('users')->export(['name' => 'été', 'hide' => 'email']);
        $this->assertSame(0, $body->tell());
        $this->assertTrue($options['stream']);
        parse_str($this->handler->getLastRequest()->getUri()->getQuery(), $parameters);
        $this->assertSame(['name' => 'été', 'hide' => 'email'], $parameters['filter']);
        $this->assertSame($csv, $this->capture($callback));
        $this->assertFalse($body->isReadable());
    }

    public function test_seekable_csv_and_binary_downloads_bypass_json_decoding()
    {
        foreach (['text/csv', 'application/octet-stream'] as $type) {
            $response = new Response(200, ['Content-Type' => $type], "Id;Name\r\n1;Alice\r\n");
            $this->assertSame($response, (new PrepareResponseBody())($response));
            $this->assertSame(0, $response->getBody()->tell());
        }
    }

    public function test_streamed_server_errors_become_sdk_exceptions()
    {
        $manager = $this->manager([$this->streamedResponse(500, '{"message":"Export query failed"}')]);
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage('Export query failed');
        $manager->grids('users')->export();
    }

    public function test_streamed_validation_errors_retain_their_details()
    {
        $manager = $this->manager([$this->streamedResponse(422, '{"errors":{"period":["Required"]}}')]);
        try {
            $manager->grids('users')->export();
            $this->fail('Expected a validation exception.');
        } catch (ValidationException $exception) {
            $this->assertSame(['period' => ['Required']], $exception->errors);
        }
    }

    public function test_expired_token_is_refreshed_before_relaying_csv()
    {
        $csv = "Id\r\n1\r\n";
        $manager = $this->manager([
            $this->streamedResponse(401, '["token_expired"]'),
            new Response(204, ['Authorization' => 'Bearer fresh-token']),
            $this->streamedResponse(200, $csv, ['Content-Type' => 'text/csv']),
        ]);
        $manager->setToken('expired-token');

        $this->assertSame($csv, $this->capture($manager->grids('users')->export()));
        $this->assertSame('fresh-token', $manager->getToken());
        $this->assertSame('Bearer fresh-token', $this->handler->getLastRequest()->getHeaderLine('Authorization'));
        $this->assertCount(0, $this->handler);
    }

    public function test_export_stream_is_closed_when_reading_fails()
    {
        $closed = false;
        $body = FnStream::decorate($this->streamedResponse(200, 'csv')->getBody(), [
            'read' => function () { throw new \RuntimeException('Socket read failed'); },
            'close' => function () use (&$closed) { $closed = true; },
        ]);
        $manager = $this->manager([new Response(200, ['Content-Type' => 'text/csv'], $body)]);

        try {
            $this->capture($manager->grids('users')->export());
            $this->fail('Expected a read failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Socket read failed', $exception->getMessage());
            $this->assertTrue($closed);
        }
    }

    public function test_normal_json_responses_are_still_decoded()
    {
        $manager = $this->manager([new Response(200, ['Content-Type' => 'application/json'], '{"data":[{"id":1}]}')]);
        $this->assertSame(1, $manager->grids('users')->data()->first()->id);
    }

    private function manager(array $responses, array &$requestOptions = []): Manager
    {
        $provider = new class($this->application) extends ServiceProvider {
            public function clientOptions(): array
            {
                return $this->options();
            }
        };
        $options = $provider->clientOptions();
        $this->handler = new MockHandler($responses);
        $handler = $this->handler;
        $options['handler']->setHandler(function ($request, array $options) use ($handler, &$requestOptions) {
            $requestOptions = $options;
            return $handler($request, $options);
        });
        $client = new Client($options);
        $manager = new Manager($client);
        $this->application->instance('idserver.client', $client);
        $this->application->instance('idserver.manager', $manager);

        return $manager;
    }

    private function streamedResponse($status, $content, array $headers = []): Response
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $content);
        rewind($stream);

        return new Response($status, $headers, new NoSeekStream(new Stream($stream)));
    }

    private function capture(\Closure $callback): string
    {
        ob_start();
        try {
            $callback();
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
