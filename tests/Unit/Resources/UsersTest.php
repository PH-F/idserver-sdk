<?php

namespace Tests\Unit\Resources;

use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Entities\User;

class UsersTest extends TestCase
{
    use Concerns\DefaultClient;
    use Concerns\MockResponse;

    /**
     * @test
     */
    public function it_can_create_a_new_user()
    {
        $this->mockResponse(201, [
            'id' => 1,
            'email' => 'jgrossi@example.com',
        ]);

        $user = $this->client->users->create([]); // Mock request

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('jgrossi@example.com', $user->email);
        $this->assertGreaterThan(0, $user->id);
    }

    /**
     * @test
     */
    public function it_can_login_a_user()
    {
        $this->markTestSkipped('Fix');

        $this->createUser('mary@example.com', 'secret');

        $user = $this->client->users->login('mary@example.com', 'secret');

        $this->assertInstanceOf(User::class, $user);

    }

    /**
     * @test
     */
    public function it_saves_the_user_jwt_in_the_session_after_login()
    {
        $this->markTestSkipped();
    }
}
