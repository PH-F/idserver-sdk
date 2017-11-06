<?php

namespace Tests\Unit\Resources;

use ReflectionMethod;
use Tests\TestCase;
use Xingo\IDServer\Entities\User as UserEntity;
use Xingo\IDServer\Resources\User;

class ResourceTest extends TestCase
{
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
        $contents = json_decode($response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(4, $contents);
        $this->assertArrayHasKey('title', $contents);
        $this->assertArrayHasKey('body', $contents);
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
}
