<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ReflectionMethod;
use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Entities\User as UserEntity;
use Xingo\IDServer\Resources\Resource;
use Xingo\IDServer\Resources;

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
        $user = app()->make(Resources\User::class);

        $method = new ReflectionMethod($user, 'makeEntity');
        $method->setAccessible(true);

        $entity = $method->invokeArgs($user, [['name' => 'John']]);

        $this->assertInstanceOf(UserEntity::class, $entity);
        $this->assertEquals('John', $entity->name);
    }

    /** @test */
    public function it_will_have_the_token_automatically_in_the_request_when_available()
    {
        $this->mockResponse(200, ['data' => ['id' => 10]]);
        $this->manager->setToken('foo');

        /** @var \GuzzleHttp\HandlerStack $handler */
        $handler = app('idserver.client')->getConfig('handler');

        $handler->after('jwt-token', Middleware::mapRequest(function (Request $request) {
            $this->assertArrayHasKey('Authorization', $request->getHeaders());
            $this->assertEquals('Bearer foo', $request->getHeaderLine('Authorization'));

            return $request;
        }));

        $this->manager->users(10)->get();
    }

    /** @test */
    public function it_is_callable_and_returns_a_resource_instance()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        $manager = app()->make('idserver.manager');
        $resource = $manager->users(1);

        $this->assertTrue(is_callable($manager->users));
        $this->assertInstanceOf(Resource::class, $resource);
        $this->assertInternalType('integer', $resource->id);
        $this->assertEquals($resource->id, $manager->users($resource->id)->id);
    }

    /** @test */
    public function it_can_have_an_instance_and_it_matches_get_method()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $manager = app()->make('idserver.manager');

        $resource = $manager->users(1);

        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $manager = app()->make('idserver.manager');
        $entity = $manager->users->get(1);

        $this->assertEquals($resource->id, $entity->id);
    }

    /** @test */
    function it_can_have_nested_resources_and_they_are_callable_as_well()
    {
        $manager = app()->make('idserver.manager');

        $tags = $manager->users(1)->tags;

        $this->assertTrue(is_callable($tags));
        $this->assertTrue(in_array(NestedResource::class, class_uses($tags)));
        $this->assertInstanceOf(Resources\User::class, $tags->parent);
        $this->assertEquals(1, $tags->parent->id);
    }

    /** @test */
    function it_gets_the_resource_name_from_class_name()
    {
        $class = app(Resources\User::class);
        $this->assertEquals('users', $class->toShortName());

        $class = app(Resources\Subscription::class);
        $this->assertEquals('subscriptions', $class->toShortName());

        $class = app(Resources\Address::class);
        $this->assertEquals('addresses', $class->toShortName());
    }
}
