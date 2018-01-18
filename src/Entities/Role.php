<?php

namespace Xingo\IDServer\Entities;

class Role extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'abilities' => Ability::class,
    ];
}
