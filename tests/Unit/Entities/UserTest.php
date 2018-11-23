<?php

namespace Tests\Unit\Entities;

use Carbon\Carbon;
use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Entities\User;

class UserTest extends TestCase
{
    use MockResponse;

    /** @test */
    public function it_can_get_the_name_of_the_user()
    {
        $user = new User([
            'first_name' => ' John ',
            'middle_name' => ' der ',
            'last_name' => ' Doe ',
        ]);

        $this->assertEquals('John der Doe', $user->name());
    }

    /** @test */
    public function it_can_get_the_name_of_the_user_with_missing_middle_and_last_name()
    {
        $user = new User([
            'first_name' => 'John',
        ]);

        $this->assertEquals('John', $user->name());
    }

    /** @test */
    public function it_can_check_if_the_user_has_permission_on_a_certain_ability()
    {
        $this->mockResponse(200, [
            'data' => [
                [
                    'name' => 'users.list',
                    'title' => 'users.list',
                ], [
                    'name' => 'users.update',
                    'title' => 'users.update',
                ]
            ],
        ]);

        $user = new User();

        $this->assertTrue($user->hasAbility('users.list'));
        $this->assertFalse($user->hasAbility('users.create'));
    }

    /** @test */
    public function it_will_return_true_if_the_user_has_access_to_everything()
    {
        $this->mockResponse(200, [
            'data' => [
                [
                    'name' => '*',
                ]
            ],
        ]);

        $user = new User();

        $this->assertTrue($user->hasAbility('users.list'));
        $this->assertTrue($user->hasAbility('users.create'));
    }

    /** @test */
    public function it_can_check_if_the_user_is_deleted()
    {
        $user = new User();

        $this->assertFalse($user->isDeleted());

        $user->deleted_at = null;
        $this->assertFalse($user->isDeleted());

        $user->deleted_at = Carbon::now();
        $this->assertTrue($user->isDeleted());
    }
}
