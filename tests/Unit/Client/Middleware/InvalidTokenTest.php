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
        $this->mockResponse(200, ['token_invalid']);
        $this->mockResponse(204, [], ['Authentication' => 'Bearer valid-token']);
        $this->mockResponse(201, ['data' => ['email' => 'john@example.com']]);

        /** @var Manager $manager */
        $manager = app('idserver.manager');
        $manager->setToken('invalid-token');

        // TODO fix this. It's not calling the same endpoint again
        $user = $manager->users->create([]);

        $this->assertEquals('valid-token', $manager->getToken());
        $this->assertEquals('john@example.com', $user->email);
    }
}
