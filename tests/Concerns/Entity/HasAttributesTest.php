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
