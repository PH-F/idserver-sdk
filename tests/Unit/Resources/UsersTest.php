<?php

namespace Tests\Unit\Resources;

use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Entities\User;
use Xingo\IDServer\Exceptions\ValidationException;

class UsersTest extends TestCase
{
    use Concerns\MockResponse;

    /**
     * @test
     */
    public function it_can_create_a_new_user()
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
    public function it_throws_an_exception_if_the_validation_failed()
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
    public function it_can_login_a_user()
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
    public function it_saves_the_user_jwt_in_the_session_after_login()
    {
        $this->mockResponse(200, [
            'token' => 'foo',
            'data' => [],
        ]);

        /** @var User $user */
        $user = $this->client->users
            ->login('john@example.com', 'secret');

        $this->assertNotEmpty($jwt = session()->get('jwt_token'));
        $this->assertEquals($jwt, $user->jwtToken());
    }
}
