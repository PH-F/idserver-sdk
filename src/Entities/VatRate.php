<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities\Traits\Discountable;

class VatRate extends Entity
{
    use Discountable;

    /**
     * @var array
     */
    protected $relationships = [
        'country' => Country::class,
    ];
}
