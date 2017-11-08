<?php

namespace Tests\Unit\Resources;

use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Client;
use Xingo\IDServer\Entities\User;
use Xingo\IDServer\Exceptions\AuthorizationException;
use Xingo\IDServer\Exceptions\ValidationException;

class UsersTest extends TestCase
{
    use Concerns\MockResponse;

    /**
     * @test
     */
    public function it_creates_a_user_with_201_status()
    {
        $this->mockResponse(201, [
            'data' => [
                'id' => 1,
                'email' => 'john@example.com',
            ],
        ]);

        $user = $this->client->users
            ->create([]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertGreaterThan(0, $user->id);
    }

    /**
     * @test
     */
    public function it_checks_validation_when_creating_user_with_422_status()
    {
        $this->mockResponse(422, [
            'errors' => ['name' => 'Name is required'],
        ]);

        $this->expectExceptionCode(422);
        $this->expectException(ValidationException::class);

        $this->client->users
            ->create([]);
    }

    /**
     * @test
     */
    public function it_logs_in_a_user_with_200_status()
    {
        $this->mockResponse(200, [
            'token' => 'foo',
            'data' => ['email' => 'john@example.com'],
        ]);

        /** @var User $user */
        $user = $this->client->users
            ->login('john@example.com', 'secret');

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function it_checks_for_login_with_401_status()
    {
        $this->mockResponse(401, ['data' => []]);

        $this->expectExceptionCode(401);
        $this->expectException(AuthorizationException::class);

        /** @var User $user */
        $user = $this->client->users
            ->login('john@example.com', 'secret');
    }

    /**
     * @test
     */
    public function it_saves_the_user_jwt_in_the_session_after_login_with_200_status()
    {
        $this->mockResponse(200, [
            'token' => 'foo',
            'data' => [],
        ]);

        /** @var User $user */
        $user = $this->client->users
            ->login('john@example.com', 'secret');

        $this->assertNotEmpty($jwt = session()->get(Client::TOKEN_NAME));
        $this->assertEquals($jwt, $user->jwtToken());
    }
}
