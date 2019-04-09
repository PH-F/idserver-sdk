<?php

namespace Xingo\IDServer\Entities;

class VatRule extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'product_groups' => VatProductGroup::class,
        'customer_groups' => VatCustomerGroup::class,
        'rates' => VatRate::class,
    ];
}
