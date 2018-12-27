<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities\Traits\Priceable;

class Variant extends Entity
{
    use Priceable;
    /**
     * @var array
     */
    protected $relationships = [
        'plan' => Plan::class,
    ];
}
