<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities\Traits\Priceable;

class ShippingCost extends Entity
{
    use Priceable;

    /**
     * @var array
     */
    protected $relationships = [
        'plan_variant' => Variant::class,
        'country' => Country::class,
    ];
}
