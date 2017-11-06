<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Tests\Concerns\MockResponse;
use Tests\TestCase;

class RequestTest extends TestCase
{
    use MockResponse;

    /**
     * @test
     */
    public function it_adds_the_store_headers_to_the_request()
    {
        /** @var Client $httpClient */
        $httpClient = app()->make(Client::class);
        $headers = $httpClient->getConfig('headers');

        $this->assertArrayHasKey('X-XINGO-Client-ID', $headers);
        $this->assertArrayHasKey('X-XINGO-Secret-Key', $headers);
        $this->assertEquals('foo', $headers['X-XINGO-Client-ID']);
        $this->assertEquals('bar', $headers['X-XINGO-Secret-Key']);
    }

    /**
     * @test
     */
    public function it_adds_the_jwt_to_the_request()
    {
        session()->put('jwt_token', 'abc123');

        /** @var Client $httpClient */
        $httpClient = app()->make(Client::class);

        $headers = $httpClient->getConfig('headers');

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertEquals('Bearer abc123', $headers['Authorization']);
    }

    /**
     * @test
     */
    public function it_do_not_add_the_jwt_if_its_not_in_the_session()
    {
        /** @var Client $httpClient */
        $httpClient = app()->make(Client::class);

        $headers = $httpClient->getConfig('headers');

        $this->assertArrayNotHasKey('Authorization', $headers);
    }

    /**
     * @test
     */
    public function the_next_request_after_login_has_the_jwt()
    {
        $this->mockResponse(200, ['token' => 'jwt123', 'data' => []]);
        $this->client->users->login('foo@bar.com', 'secret');

        /** @var Client $httpClient */
        $httpClient = app()->make(Client::class);
        $headers = $httpClient->getConfig('headers');

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertEquals('jwt123', $headers['Authorization']);
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('idserver.store.client_id', 'foo');
        $app['config']->set('idserver.store.secret_key', 'bar');
    }
}
