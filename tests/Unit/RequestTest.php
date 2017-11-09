<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Tests\Concerns\MockResponse;
use Tests\TestCase;

class RequestTest extends TestCase
{
    use MockResponse;

    /** @test */
    public function it_adds_the_store_headers_to_the_request()
    {
        /** @var Client $httpClient */
        $httpClient = app()->make('idserver.client');
        $headers = $httpClient->getConfig('headers');

        $this->assertArrayHasKey('X-XINGO-Client-ID', $headers);
        $this->assertArrayHasKey('X-XINGO-Secret-Key', $headers);
        $this->assertEquals('foo', $headers['X-XINGO-Client-ID']);
        $this->assertEquals('bar', $headers['X-XINGO-Secret-Key']);
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
