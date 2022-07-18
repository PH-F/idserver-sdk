<?php

namespace Xingo\IDServer\Entities;

class Asset extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'plan' => Plan::class,
    ];
}
