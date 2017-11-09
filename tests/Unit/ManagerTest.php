<?php

namespace Tests\Unit;

use Tests\TestCase;
use Xingo\IDServer\Resources\User;

class ManagerTest extends TestCase
{
    /** @test */
    public function it_gets_the_correct_resource_using_magic_methods()
    {
        $manager = app('idserver.manager');

        $this->assertInstanceOf(User::class, $manager->users);
    }

    /** @test */
    public function it_can_set_and_return_the_jwt_token()
    {
        $manager = app('idserver.manager');
        $manager->setToken('my-token');

        $this->assertEquals('my-token', $manager->getToken());
    }

    /** @test */
    public function it_will_return_an_empty_string_when_no_token_is_set_in_the_session()
    {
        $manager = app('idserver.manager');

        $this->assertEquals('', $manager->getToken());
    }
}
