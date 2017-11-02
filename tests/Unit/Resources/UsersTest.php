<?php

namespace Tests\Unit\Resources;

use Tests\Concerns\ResourceFactory;
use Tests\TestCase;
use Xingo\IDServer\Entity\User;

class UsersTest extends TestCase
{
    use ResourceFactory;

    /**
     * @test
     */
    public function it_can_create_a_new_user()
    {
        $user = $this->client->users->create([
            'email' => 'john@example.com',
            'password' => 'secret',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertGreaterThan(0, $user->id);
    }

    /**
     * @test
     */
    public function it_can_login_a_user()
    {
        $this->markTestSkipped();

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
