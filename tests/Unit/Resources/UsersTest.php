<?php

namespace Tests\Unit\Resources;

use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Manager;
use Xingo\IDServer\Entities\User;
use Xingo\IDServer\Exceptions\AuthorizationException;
use Xingo\IDServer\Exceptions\ValidationException;

class UsersTest extends TestCase
{
    use Concerns\MockResponse;

    /**
     * @test
     */
    public function user_create_with_201_status()
    {
        $this->mockResponse(201, [
            'data' => [
                'id' => 1,
                'email' => 'john@example.com',
        ]);

        $user = $this->manager->users
            ->create([]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertGreaterThan(0, $user->id);
    }

    /**
     * @test
     */
    public function user_create_with_422_status()
    {
        $this->mockResponse(422, [
            'errors' => ['name' => 'Name is required'],
        ]);

        $this->expectExceptionCode(422);
        $this->expectException(ValidationException::class);

        $this->manager->users
            ->create([]);
    }

    /**
     * @test
     */
    public function user_login_with_200_status()
    {
        $this->mockResponse(200, [
            'token' => 'foo',
            'data' => ['email' => 'john@example.com'],
        ]);

        /** @var User $user */
        $user = $this->manager->users
            ->login('john@example.com', 'secret');

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function user_login_with_401_status()
    {
        $this->mockResponse(401, ['data' => []]);

        $this->expectExceptionCode(401);
        $this->expectException(AuthorizationException::class);

        /** @var User $user */
        $user = $this->manager->users
            ->login('john@example.com', 'secret');
    }

    /**
     * @test
     */
    public function user_login_saves_the_jwt_in_the_session()
    {
        $this->mockResponse(200, [
            'token' => 'foo',
            'data' => [],
        ]);

        /** @var User $user */
        $user = $this->manager->users
            ->login('john@example.com', 'secret');

        $this->assertNotEmpty($jwt = session()->get(Manager::TOKEN_NAME));
        $this->assertEquals($jwt, $user->jwtToken());
    }
}
