<?php

namespace Tests\Unit\Client\Middleware;

use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Manager;

class InvalidTokenTest extends TestCase
{
    use MockResponse;

    /** @test */
    function it_will_refresh_the_token_if_it_is_invalid()
    {
        $this->mockResponse(200, ['token_expired']);
        $this->mockResponse(204, [], ['Authorization' => 'Bearer valid-token']);
        $this->mockResponse(201, ['data' => ['email' => 'john@example.com']]);

        /** @var Manager $manager */
        $manager = app('idserver.manager');
        $manager->setToken('invalid-token');

        $user = $manager->users->create([]);

        $this->assertEquals('valid-token', $manager->getToken());
        $this->assertEquals('john@example.com', $user->email);
    }

    /** @test */
    function it_will_not_refresh_the_token_with_normal_response()
    {
        $this->mockResponse(201);

        /** @var Manager $manager */
        $manager = app('idserver.manager');
        $manager->setToken('valid-token');

        $manager->users->create([]);

        $this->assertEquals('valid-token', $manager->getToken());
    }
}
