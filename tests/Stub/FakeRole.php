<?php

namespace Tests\Stub;

class FakeRole extends \Xingo\IDServer\Entities\Role
{
    /**
     * @var array
     */
    protected static $relations = [
        'abilities' => FakeAbility::class,
    ];
}
