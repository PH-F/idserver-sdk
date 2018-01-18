<?php

namespace Xingo\IDServer\Entities;

class Role extends Entity
{
    /**
     * @var array
     */
    protected static $relations = [
        'abilities' => Ability::class,
    ];
}
