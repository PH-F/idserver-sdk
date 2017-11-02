<?php

namespace Tests\Unit\Resources;

use ReflectionMethod;
use Tests\TestCase;
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

        $this->assertCount(4, $response);
        $this->assertArrayHasKey('title', $response);
        $this->assertArrayHasKey('body', $response);
    }
}
