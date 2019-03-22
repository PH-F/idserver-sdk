<?php

namespace Tests\Concerns;

use Tests\TestCase;

class HasAttributesTest extends TestCase
{
    /** @test */
    public function it_can_have_accessors()
    {
        $user = new TempUser([
            'first' => 'John',
            'last' => 'Doe',
        ]);

        $this->assertEquals('John Doe', $user->full_name);

        $user = new TempUser([
            'first' => 'John',
        ]);

        $this->assertEquals('John', $user->full_name);
    }

    /** @test */
    public function it_can_have_mutators()
    {
        $user = new TempUser();
        $user->first = 'john';

        $this->assertEquals('John', $user->first);
    }

    /** @test */
    public function it_can_read_date_as_iso_string()
    {
        config()->set('app.timezone', 'Europe/Amsterdam');

        $user = new TempUser(['created_at' => '2018-10-10T08:15:23.00000Z']);

        $this->assertEquals('2018-10-10 10:15:23', $user->created_at->toDateTimeString());
    }

    /** @test */
    public function it_can_read_date_as_array()
    {
        config()->set('app.timezone', 'Australia/Perth');

        $user = new TempUser(['created_at' => [
            'date' => '2019-03-08 10:35:47.000000',
            'timezone_type' => 3,
            'timezone' => 'Europe/Amsterdam',
        ]]);

        $this->assertEquals('2019-03-08 17:35:47', $user->created_at->toDateTimeString());
    }
}

class TempUser extends \Xingo\IDServer\Entities\Entity
{
    public function getFullNameAttribute($value)
    {
        return trim($this->first . ' ' . $this->last);
    }

    public function setFirstAttribute($value)
    {
        $this->attributes['first'] = ucfirst($value);
    }
}
