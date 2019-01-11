<?php

namespace Xingo\IDServer\Entities;

class Coupon extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'promotion' => Promotion::class,
    ];
}
