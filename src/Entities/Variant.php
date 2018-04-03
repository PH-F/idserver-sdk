<?php

namespace Xingo\IDServer\Entities;

class Variant extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'plan' => Plan::class,
    ];
}
