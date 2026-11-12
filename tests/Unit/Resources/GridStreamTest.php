<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\NoSeekStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Xingo\IDServer\Client\Middleware\JwtToken;
use Xingo\IDServer\Client\Middleware\PrepareResponseBody;
use Xingo\IDServer\Client\Middleware\TokenExpired;
use Xingo\IDServer\Exceptions\ServerException;
use Xingo\IDServer\Exceptions\ValidationException;
use Xingo\IDServer\Manager;

class GridStreamTest extends TestCase
{
    private $handler;

    public function test_relays_csv_without_decoding_or_reading_it_before_the_callback()
    {
        $csv = "Id;Name\r\n" . str_repeat("1;été,semicolon;quote\"\r\n", 200);
        $response = $this->streamedResponse(200, $csv, ['Content-Type' => 'text/csv']);
        $body = $response->getBody();
        $history = [];
        $manager = $this->manager([$response], $history);
        $callback = $manager->grids('users')->export(['name' => 'été', 'hide' => 'email']);
        $this->assertSame(0, $body->tell());
        $this->assertTrue($history[0]['options']['stream']);
        $this->assertStringContainsString('filter%5Bhide%5D=email', $history[0]['request']->getUri()->getQuery());
        ob_start();
        try {
            $callback();
            $this->assertSame($csv, ob_get_contents());
        } finally {
            ob_end_clean();
        }
        $this->assertFalse($body->isReadable());
    }

    public function test_seekable_csv_also_bypasses_json_token_inspection()
    {
        $response = new Response(200, ['Content-Type' => 'text/csv'], "Id;Name\r\n1;Alice\r\n");
        $this->assertSame($response, (new PrepareResponseBody())($response));
        $this->assertFalse((new TokenExpired())->shouldRefreshToken(new \GuzzleHttp\Psr7\Request('GET', '/export'), $response));
    }

    public function test_streamed_server_errors_become_sdk_exceptions()
    {
        $history = [];
        $manager = $this->manager([$this->streamedResponse(500, '{"message":"Export query failed"}')], $history);
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage('Export query failed');
        $manager->grids('users')->export();
    }

    public function test_streamed_validation_errors_retain_their_details()
    {
        $history = [];
        $manager = $this->manager([$this->streamedResponse(422, '{"errors":{"period":["Required"]}}')], $history);
        try {
            $manager->grids('users')->export();
            $this->fail('Expected a validation exception.');
        } catch (ValidationException $exception) {
            $this->assertSame(['period' => ['Required']], $exception->errors);
        }
    }

    public function test_expired_token_is_refreshed_before_relaying_csv()
    {
        Event::fake();
        $history = [];
        $manager = $this->manager([
            $this->streamedResponse(401, '["token_expired"]'),
            new Response(204, ['Authorization' => 'Bearer fresh-token']),
            $this->streamedResponse(200, "Id\r\n1\r\n", ['Content-Type' => 'text/csv']),
        ], $history);
        $manager->setToken('expired-token');
        $callback = $manager->grids('users')->export();
        ob_start();
        try {
            $callback();
            $this->assertSame("Id\r\n1\r\n", ob_get_contents());
        } finally {
            ob_end_clean();
        }
        $this->assertCount(3, $history);
        $this->assertSame('fresh-token', $manager->getToken());
        $this->assertSame('Bearer fresh-token', $this->handler->getLastRequest()->getHeaderLine('Authorization'));
    }

    private function manager(array $responses, array &$history): Manager
    {
        $this->handler = new MockHandler($responses);
        $stack = HandlerStack::create($this->handler);
        $stack->push(Middleware::history($history));
        $stack->push(new JwtToken());
        $stack->push(new TokenExpired());
        $stack->push(Middleware::mapResponse(new PrepareResponseBody()));
        $client = new Client(['base_uri' => 'https://idserver.test/', 'handler' => $stack]);
        $manager = new Manager($client);
        app()->instance('idserver.client', $client);
        app()->instance('idserver.manager', $manager);

        return $manager;
    }

    private function streamedResponse($status, $content, array $headers = []): Response
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $content);
        rewind($stream);

        return new Response($status, $headers, new NoSeekStream(new Stream($stream)));
    }
}
