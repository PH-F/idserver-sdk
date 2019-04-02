<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities\Traits\Discountable;

class VatRule extends Entity
{
    use Discountable;

    /**
     * @var array
     */
    protected $relationships = [
        'product_groups' => VatProductGroup::class,
        'rates' => VatRate::class,
    ];
}
