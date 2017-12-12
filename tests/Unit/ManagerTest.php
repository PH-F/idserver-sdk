<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Tests\TestCase;
use Xingo\IDServer\Manager;
use Xingo\IDServer\Resources\Resource;
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
    public function it_can_set_and_remove_the_jwt_token()
    {
        $manager = app('idserver.manager');
        $manager->setToken('my-token');

        $this->assertEquals('my-token', $manager->getToken());

        $manager->removeToken();

        $this->assertEquals(null, $manager->getToken());
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
        $this->assertEquals('foo-web', $headers['X-XINGO-Client-ID']);
        $this->assertEquals('bar-web', $headers['X-XINGO-Secret-Key']);
    }

    /** @test */
    public function it_adds_the_store_cli_headers_when_forced_to()
    {
        $this->app['env'] = 'foo';

        /** @var Client $httpClient */
        $httpClient = app()->make('idserver.client');

        $headers = $httpClient->getConfig('headers');

        $this->assertArrayHasKey('X-XINGO-Client-ID', $headers);
        $this->assertArrayHasKey('X-XINGO-Secret-Key', $headers);
        $this->assertEquals('foo-cli', $headers['X-XINGO-Client-ID']);
        $this->assertEquals('bar-cli', $headers['X-XINGO-Secret-Key']);
    }

    /** @test */
    public function it_can_be_called_using_the_ids_helper()
    {
        $this->assertTrue(function_exists('ids'));
        $this->assertInstanceOf(Manager::class, ids());
        $this->assertInstanceOf(Resource::class, ids()->users);
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('idserver.store.web.client_id', 'foo-web');
        $app['config']->set('idserver.store.web.secret_key', 'bar-web');
        $app['config']->set('idserver.store.cli.client_id', 'foo-cli');
        $app['config']->set('idserver.store.cli.secret_key', 'bar-cli');
    }
}
