<?php

namespace Xingo\IDServer\Entities\Plan;

use Xingo\IDServer\Entities\Entity;

class Duration extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'plan_variant' => Variant::class,
    ];
}
