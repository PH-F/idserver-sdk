<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Xingo\IDServer\Entities\User;

class UserTest extends TestCase
{
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
}
