<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Xingo\IDServer\Client;
use Xingo\IDServer\ServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = app(Client::class);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    /**
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('idserver.url', 'http://example.com');
    }
}
