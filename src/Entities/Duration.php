<?php

namespace Xingo\IDServer\Entities;

class Duration extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'plan_variant' => Variant::class,
    ];
}
