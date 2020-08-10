<?php

namespace Xingo\IDServer\Entities;

class Effort extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'plan_variant' => Variant::class,
    ];
}
