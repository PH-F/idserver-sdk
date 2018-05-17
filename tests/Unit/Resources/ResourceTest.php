<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ReflectionMethod;
use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities\User as UserEntity;
use Xingo\IDServer\Exceptions;
use Xingo\IDServer\Resources;
use Xingo\IDServer\Resources\Resource;

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
    public function it_will_send_a_json_accept_header()
    {
        $this->mockResponse();

        $user = $this->manager->users->all();

        $this->assertRequest(function (Request $request) {
            $this->assertTrue($request->hasHeader('Accept'));
            $this->assertContains('application/json', $request->getHeader('Accept'));
        });
    }

    /** @test */
    public function it_can_make_an_entity_instance()
    {
        $user = app()->make(Resources\User::class);

        $method = new ReflectionMethod($user, 'makeEntity');
        $method->setAccessible(true);

        $entity = $method->invokeArgs($user, [['name' => 'John']]);

        $this->assertInstanceOf(UserEntity::class, $entity);
        $this->assertInstanceOf(IdsEntity::class, $entity);
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
    public function it_accepts_a_string_as_invoke_parameter()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $manager = app()->make('idserver.manager');

        $resource = $manager->users('1');

        $this->assertEquals($resource->id, 1);
    }

    /** @test */
    public function it_accepts_an_object_as_invoke_parameter()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $manager = app()->make('idserver.manager');

        $user = (object)['id' => 1];
        $resource = $manager->users($user);

        $this->assertEquals($resource->id, 1);
    }

    /** @test */
    public function it_can_have_nested_resources_and_they_are_callable_as_well()
    {
        $manager = app()->make('idserver.manager');

        $tags = $manager->users(1)->tags;

        $this->assertTrue(is_callable($tags));
        $this->assertTrue(in_array(NestedResource::class, class_uses($tags)));
        $this->assertInstanceOf(Resources\User::class, $tags->parent);
        $this->assertEquals(1, $tags->parent->id);
    }

    /** @test */
    public function it_throws_an_exception_if_the_uri_is_wrong()
    {
        $this->mockResponse(404);

        $this->expectException(Exceptions\NotFoundException::class);

        $this->manager->users(1)->get();
    }

    /** @test */
    public function it_gets_the_resource_name_from_class_name()
    {
        $class = app(Resources\User::class);
        $this->assertEquals('users', $class->toShortName());

        $class = app(Resources\Subscription::class);
        $this->assertEquals('subscriptions', $class->toShortName());

        $class = app(Resources\Address::class);
        $this->assertEquals('addresses', $class->toShortName());
    }

    /** @test */
    public function it_paginates_using_a_custom_method()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 2],
            ],
            'meta' => [
                'current_page' => 2,
                'per_page' => 1,
                'total' => 3
            ]
        ]);

        $collection = $this->manager->users
            ->paginate(2, 1)
            ->all();

        $this->assertCount(1, $collection);
        $this->assertEquals(2, $collection->first()->id);
        $this->assertInstanceOf('stdClass', $collection->meta);
        $this->assertEquals(1, $collection->meta->per_page);
        $this->assertEquals(3, $collection->meta->total);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users', $request->getUri()->getPath());
            $this->assertEquals('page=2&per_page=1', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_allows_to_disable_pagination_when_necessary()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->users
            ->paginate(false)
            ->all();

        $this->assertCount(2, $collection);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users', $request->getUri()->getPath());
            $this->assertEquals('page=1&per_page=-1', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_can_be_sorted()
    {
        $this->mockResponse(200);
        $this->mockResponse(200);

        $this->manager->users
            ->paginate(2, 1)
            ->sort('foo', 'asc')
            ->all();

        $this->assertRequest(function (Request $request) {
            $query = 'page=2&per_page=1&sort=' . urlencode('+foo');
            $this->assertEquals($query, $request->getUri()->getQuery());
        });

        $this->manager->users
            ->paginate(3, 2)
            ->sort([
                'foo' => 'asc',
                'bar' => 'desc',
            ])
            ->all();

        $this->assertRequest(function (Request $request) {
            $this->assertEquals(http_build_query([
                'page' => 3,
                'per_page' => 2,
                'sort' => '+foo,-bar',
            ]), $request->getUri()->getQuery());
        });
    }
}
