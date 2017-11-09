<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\HandlerStack;
use ReflectionMethod;
use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Entities\User as UserEntity;
use Xingo\IDServer\Resources\User;

class ResourceTest extends TestCase
{
    use MockResponse;

    /**
     * @test
     */
    public function it_returns_an_array_when_calling_a_json_endpoint()
    {
        $uri = 'https://jsonplaceholder.typicode.com/posts/1';

        $user = app()->make(User::class);

        $method = new ReflectionMethod($user, 'call');
        $method->setAccessible(true);

        $response = $method->invokeArgs($user, ['GET', $uri]);

        $this->markTestIncomplete('This is changed to a guzzle response and test needs to be rewritten.');

        $this->assertCount(5, $response);
        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('body', $response);
    }

    /**
     * @test
     */
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
    function it_will_have_the_token_automatically_in_the_request_when_available()
    {
        $handler = app('idserver.client')->getConfig('handler');

        // Try to remove the jwt-token middleware. If that middleware is not available it will throw an exception.
        $handler->before('jwt-token', function ($request) {
            // Fake middleware
        });

        $this->assertTrue(true);
    }
}
