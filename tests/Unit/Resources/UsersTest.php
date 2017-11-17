<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Intervention\Image\ImageManager;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Entities\User;
use Xingo\IDServer\Exceptions;
use Xingo\IDServer\Manager;

class UsersTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    function it_creates_a_user_with_201_status()
    {
        $this->mockResponse(201, [
            'data' => $data = [
                'id' => 1,
                'email' => 'john@example.com',
            ],
        ]);

        $user = $this->manager->users
            ->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertGreaterThan(0, $user->id);

        $this->assertRequest(function (Request $request) use ($data) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users', $request->getUri()->getPath());
            $this->assertParamsEquals($request, $data);
        });
    }

    /** @test */
    function it_checks_validation_when_creating_user_with_422_status()
    {
        $this->mockResponse(422, [
            'errors' => ['name' => 'Name is required'],
        ]);

        $this->expectExceptionCode(422);
        $this->expectException(Exceptions\ValidationException::class);

        $this->manager->users
            ->create([]);
    }

    /** @test */
    function it_gets_a_user_with_a_200_status()
    {
        $this->mockResponse(200, [
            'data' => [
                'id' => 1,
                'email' => 'john@example.com',
            ],
        ]);

        $user = $this->manager->users(1)
            ->get();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals(1, $user->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    function it_updates_a_user_with_a_200_status()
    {
        $this->mockResponse(200, [
            'data' => [
                'id' => 1,
                'email' => 'john@example.com',
                'first_name' => 'foo',
            ],
        ]);

        $user = $this->manager->users(1)->update($data = [
            'first_name' => 'foo'
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('foo', $user->first_name);
        $this->assertEquals(1, $user->id);

        $this->assertRequest(function (Request $request) use ($data) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('users/1', $request->getUri()->getPath());
            $this->assertParamsEquals($request, $data);
        });
    }

    /** @test */
    function it_will_throw_a_validation_exception_when_updating_a_user_returns_validation_errors()
    {
        $this->mockResponse(422, [
            'message' => 'The given data is invalid',
            'errors' => [
                'email' => [
                    'The email field is required.'
                ],
            ],
        ]);

        $this->expectExceptionCode(422);
        $this->expectException(Exceptions\ValidationException::class);

        $this->manager->users(1)->update([
            'email' => ''
        ]);
    }

    /** @test */
    function it_logs_in_a_user_with_200_status()
    {
        $this->mockResponse(200, [
            'token' => 'foo',
            'data' => ['email' => 'john@example.com'],
        ]);

        /** @var User $user */
        $user = $this->manager->users
            ->login('john@example.com', 'secret');

        $this->assertInstanceOf(User::class, $user);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('auth/login', $request->getUri()->getPath());
            $this->assertParamsEquals($request, [
                'email' => 'john@example.com',
                'password' => 'secret',
            ]);
        });
    }

    /** @test */
    function it_checks_for_login_with_401_status()
    {
        $this->mockResponse(401, ['data' => []]);

        $this->expectExceptionCode(401);
        $this->expectException(Exceptions\AuthorizationException::class);

        $this->manager->users
            ->login('john@example.com', 'secret');
    }

    /** @test */
    function it_saves_the_user_jwt_in_the_session_after_login_with_200_status()
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

    /** @test */
    function it_can_refresh_the_jwt()
    {
        $this->mockResponse(200, [], ['Authentication' => 'Bearer new-token']);

        $this->manager->users->refreshToken();

        $this->assertEquals('new-token', $this->manager->getToken());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('auth/refresh', $request->getUri()->getPath());
        });
    }

    /** @test */
    function it_can_get_a_user_by_get_method()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $this->mockResponse(200, ['data' => ['id' => 2]]);

        $user = $this->manager->users(1)->get();
        $this->assertEquals(1, $user->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    function it_can_be_deleted()
    {
        $this->mockResponse(204);
        $this->mockResponse(204);

        $result = $this->manager->users(1)->delete();
        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('DELETE', $request->getMethod());
            $this->assertEquals('users/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    function it_can_throws_a_500_exception_error()
    {
        $this->mockResponse(500);

        $this->expectException(Exceptions\ServerException::class);

        $this->manager->users->delete(1);
    }

    /** @test */
    function it_can_be_confirmed()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $user = $this->manager->users(1)->confirm('fake-token');
        $this->assertEquals(1, $user->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PATCH', $request->getMethod());
            $this->assertEquals('users/1/confirm', $request->getUri()->getPath());
            $this->assertParamsEquals($request, ['token' => 'fake-token']);
        });

        $this->mockResponse(422, ['errors' => ['token' => 'Required']]);
        $this->expectException(Exceptions\ValidationException::class);
        $this->manager->users(1)->confirm('fake-token');
    }

    /** @test */
    function it_can_change_avatar()
    {
        $this->mockResponse(200, [
            'user' => ['id' => 1],
            'avatar' => ['url' => 'http://google.com'],
        ]);

        $user = $this->manager->users(1)
            ->changeAvatar('http://placehold.it/30x30');

        $this->assertEquals(1, $user->id);
        $this->assertArrayHasKey('url', $user->avatar);
        $this->assertEquals('http://google.com', $user->avatar['url']);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PATCH', $request->getMethod());
            $this->assertEquals('users/1/avatar', $request->getUri()->getPath());
            $this->assertParamsEquals($request, [
                'avatar' => base64_encode(
                    (new ImageManager())
                        ->make('http://placehold.it/30x30')
                        ->stream()
                ),
            ]);
        });
    }

    /** @test */
    function it_can_have_tags()
    {
        $this->mockResponse(200, [
            'tags' => ['foo', 'bar'],
            'user' => ['id' => 1],
        ]);

        $user = $this->manager->users(1)->tags();

        $this->assertEquals(1, $user->id);
        $this->assertEquals(['foo', 'bar'], $user->tags);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users/1/tags', $request->getUri()->getPath());
        });
    }

    /** @test */
    function it_can_reset_the_password()
    {
        $this->mockResponse(201, [
            'user_id' => 2,
            'token' => 'temporary-token',
        ]);

        $token = $this->manager->users(2)->resetPassword();

        $this->assertEquals('temporary-token', $token);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/2/reset-password', $request->getUri()->getPath());
        });
    }

    /** @test */
    function it_can_update_the_password()
    {
        $this->mockResponse(204);

        $result = $this->manager->users(3)
            ->changePassword('fake-token', 'abc123');

        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PATCH', $request->getMethod());
            $this->assertEquals('users/3/change-password', $request->getUri()->getPath());
            $this->assertParamsEquals($request, [
                'token' => 'fake-token',
                'password' => 'abc123',
            ]);
        });
    }
}
