<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Response;
use ReflectionMethod;
use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Entities\User as UserEntity;
use Xingo\IDServer\Resources\Resource;
use Xingo\IDServer\Resources\User;

class ResourceTest extends TestCase
{
    use MockResponse;

    /** @test */
    public function it_returns_a_psr7_response_when_calling_a_json_endpoint()
    {
        $this->mockResponse();

        $uri = 'https://jsonplaceholder.typicode.com/posts/1';

        $user = $this->manager->users;

        $method = new ReflectionMethod($user, 'call');
        $method->setAccessible(true);

        $response = $method->invokeArgs($user, ['GET', $uri]);

        $this->assertInstanceOf(Response::class, $response);
    }

    /** @test */
    public function it_can_make_an_entity_instance()
    {
        $user = app()->make(User::class);

        $method = new ReflectionMethod($user, 'makeEntity');
        $method->setAccessible(true);

        $entity = $method->invokeArgs($user, [['name' => 'John']]);

        $this->assertInstanceOf(UserEntity::class, $entity);
        $this->assertEquals('John', $entity->name);
    }

    /** @test */
    public function it_will_have_the_token_automatically_in_the_request_when_available()
    {
        $this->markTestSkipped('Fix');

        $handler = app('idserver.client')->getConfig('handler');

        // Try to remove the jwt-token middleware. If that middleware is not available it will throw an exception.
        $handler->before('jwt-token', function ($request) {
            // Fake middleware
        });

        $this->assertTrue(true);
    }

    /** @test */
    public function it_is_callable_and_returns_a_resource_instance()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $manager = app()->make('idserver.manager');

        $this->assertTrue(is_callable($manager->users));
        $this->assertInstanceOf(Resource::class, $manager->users(1));
    }

    /** @test */
    public function it_can_have_an_instance_and_it_matches_get_method()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $manager = app()->make('idserver.manager');

        $resource = $manager->users(1);

        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $manager = app()->make('idserver.manager');

        $this->assertEquals($resource->instance, $manager->users->get(1));
    }
}
