<?php

namespace Xingo\IDServer\Entities;

class Note extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'user' => User::class,
    ];
}
