<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities\Traits\Priceable;

class Plan extends Entity
{
    use Priceable;

    /**
     * @var array
     */
    protected $relationships = [
        'stores' => Store::class,
    ];
}
