<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Tests\TestCase;
use Xingo\IDServer\Resources\User;

class ManagerTest extends TestCase
{
    /** @test */
    public function it_gets_the_correct_resource_using_magic_methods()
    {
        $manager = app('idserver.manager');

        $this->assertInstanceOf(User::class, $manager->users);
    }

    /** @test */
    public function it_can_set_and_return_the_jwt_token()
    {
        $manager = app('idserver.manager');
        $manager->setToken('my-token');

        $this->assertEquals('my-token', $manager->getToken());
    }

    /** @test */
    public function it_will_return_an_empty_string_when_no_token_is_set_in_the_session()
    {
        $manager = app('idserver.manager');

        $this->assertEquals('', $manager->getToken());
    }

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
