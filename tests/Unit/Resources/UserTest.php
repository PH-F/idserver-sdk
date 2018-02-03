<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Intervention\Image\ImageManager;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities\Ability;
use Xingo\IDServer\Entities\Address;
use Xingo\IDServer\Entities\User;
use Xingo\IDServer\Exceptions;
use Xingo\IDServer\Manager;
use Xingo\IDServer\Resources\Collection;

class UserTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_creates_a_user_with_201_status()
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
        $this->assertInstanceOf(IdsEntity::class, $user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertGreaterThan(0, $user->id);

        $this->assertRequest(function (Request $request) use ($data) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($data), $request->getBody());
        });
    }

    /** @test */
    public function it_checks_validation_when_creating_user_with_422_status()
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
    public function it_gets_a_user_with_a_200_status()
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
        $this->assertInstanceOf(IdsEntity::class, $user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals(1, $user->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_gets_all_users()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->users->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(User::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals(2, $collection->last()->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_can_be_filtered_by_id()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 3],
                ['id' => 5],
                ['id' => 7],
            ],
        ]);

        $users = $this->manager->users(3, 5, 7)->get();

        $this->assertCount(3, $users);

        $users->each(function (User $user) {
            $this->assertTrue(in_array($user->id, [3, 5, 7]));
        });

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users', $request->getUri()->getPath());
            $this->assertEquals(http_build_query(['ids' => '3,5,7']), $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_can_be_filtered_by_id_using_an_array()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 3],
                ['id' => 5],
                ['id' => 7],
            ],
        ]);

        $users = $this->manager->users([3, 5, 7])->get();

        $this->assertCount(3, $users);

        $users->each(function (User $user) {
            $this->assertTrue(in_array($user->id, [3, 5, 7]));
        });

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users', $request->getUri()->getPath());
            $this->assertEquals(http_build_query(['ids' => '3,5,7']), $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_can_be_filtered_using_multiple_filters()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 3],
            ],
        ]);

        $users = $this->manager->users
            ->all($filters = [
                'first_name' => 'foo',
                'username' => 'bar',
            ]);

        $this->assertCount(1, $users);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users', $request->getUri()->getPath());

            $this->assertEquals(
                'first_name=foo&username=bar&page=1&per_page=10',
                $request->getUri()->getQuery()
            );
        });
    }

    /** @test */
    public function it_updates_a_user_with_a_200_status()
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
        $this->assertInstanceOf(IdsEntity::class, $user);
        $this->assertEquals('foo', $user->first_name);
        $this->assertEquals(1, $user->id);

        $this->assertRequest(function (Request $request) use ($data) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('users/1', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($data), $request->getBody());
        });
    }

    /** @test */
    public function it_will_throw_a_validation_exception_when_updating_a_user_returns_validation_errors()
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
    public function it_logs_in_a_user_with_200_status()
    {
        $this->mockResponse(200, [
            'token' => 'foo',
            'data' => ['email' => 'john@example.com'],
        ]);

        /** @var User $user */
        $user = $this->manager->users
            ->login('john@example.com', 'secret');

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(IdsEntity::class, $user);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('auth/login', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'email' => 'john@example.com',
                'password' => 'secret',
            ]), $request->getBody());
        });
    }

    /** @test */
    public function it_can_send_a_claims_array_to_the_login_endpoint()
    {
        $this->mockResponse(200, ['token' => 'bar']);

        $this->manager->users
            ->login('john@example.com', 'secret', [
                'foo' => true,
            ]);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals(http_build_query([
                'email' => 'john@example.com',
                'password' => 'secret',
                'claims' => ['foo' => true],
            ]), (string)$request->getBody());
        });
    }

    /** @test */
    public function it_checks_for_login_with_401_status()
    {
        $this->mockResponse(401, ['data' => []]);

        $this->expectExceptionCode(401);
        $this->expectException(Exceptions\AuthorizationException::class);

        $this->manager->users
            ->login('john@example.com', 'secret');
    }

    /** @test */
    public function it_saves_the_user_jwt_in_the_session_after_login_with_200_status()
    {
        $this->mockResponse(200, [
            'token' => 'foo',
            'data' => [],
        ]);

        /** @var User $user */
        $user = $this->manager->users
            ->login('john@example.com', 'secret');

        $this->assertNotEmpty($jwt = $this->manager->getToken());
        $this->assertEquals($jwt, $user->jwtToken());
    }

    /** @test */
    public function it_can_refresh_the_jwt()
    {
        $this->mockResponse(200, [], ['Authorization' => 'Bearer new-token']);

        $this->manager->users->refreshToken();

        $this->assertEquals('new-token', $this->manager->getToken());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('auth/refresh', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_can_get_a_user_by_get_method()
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
    public function it_can_be_deleted()
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
    public function it_can_throws_a_500_exception_error()
    {
        $this->mockResponse(500);

        $this->expectException(Exceptions\ServerException::class);

        $this->manager->users(1)->delete();
    }

    /** @test */
    public function it_throws_a_throttle_exception_if_429_status_code_is_returned()
    {
        $this->mockResponse(429);

        $this->expectException(Exceptions\ThrottleException::class);

        $this->manager->users(2)->delete();
    }

    /** @test */
    public function it_can_be_confirmed()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);
        $user = $this->manager->users(1)->confirm('fake-token');

        $this->assertInstanceOf(IdsEntity::class, $user);
        $this->assertEquals(1, $user->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PATCH', $request->getMethod());
            $this->assertEquals('users/1/confirm', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'token' => 'fake-token',
            ]), $request->getBody());
        });

        $this->mockResponse(422, ['errors' => ['token' => 'Required']]);
        $this->expectException(Exceptions\ValidationException::class);
        $this->manager->users(1)->confirm('fake-token');
    }

    /** @test */
    public function it_can_change_avatar()
    {
        $this->mockResponse(200, [
            'user' => ['id' => 1],
            'avatar' => ['url' => 'http://google.com'],
        ]);

        $user = $this->manager->users(1)
            ->changeAvatar('http://placehold.it/30x30');

        $this->assertInstanceOf(IdsEntity::class, $user);
        $this->assertEquals(1, $user->id);
        $this->assertArrayHasKey('url', $user->avatar);
        $this->assertEquals('http://google.com', $user->avatar['url']);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PATCH', $request->getMethod());
            $this->assertEquals('users/1/avatar', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'avatar' => base64_encode(
                    (new ImageManager())
                        ->make('http://placehold.it/30x30')
                        ->stream()
                ),
            ]), $request->getBody());
        });
    }

    /** @test */
    public function it_can_have_tags()
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
    public function it_can_have_addresses()
    {
        $this->mockResponse(200, [
            'data' => [
                ['street' => 'foo'],
                ['street' => 'bar'],
            ],
        ]);

        $collection = $this->manager->users(1)->addresses();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Address::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals('foo', $collection->first()->street);
        $this->assertEquals('bar', $collection->last()->street);
    }

    /** @test */
    public function it_can_list_user_abilities()
    {
        $this->mockResponse(200, [
            'data' => [
                ['name' => 'foo.create'],
                ['name' => 'bar.update'],
            ],
        ]);

        $collection = $this->manager->users(1)->abilities();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Ability::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals('foo.create', $collection->first()->name);
        $this->assertEquals('bar.update', $collection->last()->name);
    }

    /** @test */
    public function it_can_reset_the_password_using_id()
    {
        $this->mockResponse(201, [
            'user_id' => 2,
            'token' => 'temporary-token',
        ]);

        $token = $this->manager->users->resetPassword(2);

        $this->assertEquals('temporary-token', $token);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/reset-password', $request->getUri()->getPath());
            $this->assertEquals('id=2', $request->getBody());
        });
    }

    /** @test */
    public function it_can_reset_the_password_using_email()
    {
        $this->mockResponse(201, [
            'user_id' => 2,
            'token' => 'temporary-token',
        ]);

        $token = $this->manager->users->resetPassword('foo@bar.com');

        $this->assertEquals('temporary-token', $token);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/reset-password', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'email' => 'foo@bar.com',
            ]), (string)$request->getBody());
        });
    }

    /** @test */
    public function it_can_update_the_password_using_id()
    {
        $this->mockResponse(204);

        $result = $this->manager->users
            ->updatePassword('10', 'fake-token', 'abc123');

        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PATCH', $request->getMethod());
            $this->assertEquals('users/update-password', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'id' => 10,
                'token' => 'fake-token',
                'password' => 'abc123',
            ]), $request->getBody());
        });
    }

    /** @test */
    public function it_can_update_the_password_using_email()
    {
        $this->mockResponse(204);

        $result = $this->manager->users(3)
            ->updatePassword('foo@bar.com', 'fake-token', 'abc123');

        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PATCH', $request->getMethod());
            $this->assertEquals('users/update-password', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'email' => 'foo@bar.com',
                'token' => 'fake-token',
                'password' => 'abc123',
            ]), $request->getBody());
        });
    }

    /** @test */
    public function it_can_change_the_password()
    {
        $this->mockResponse(204);

        $result = $this->manager->users(4)
            ->changePassword('secret');

        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PATCH', $request->getMethod());
            $this->assertEquals('users/4/change-password', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'password' => 'secret',
            ]), $request->getBody());
        });
    }
}
