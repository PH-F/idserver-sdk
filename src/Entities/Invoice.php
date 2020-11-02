<?php

namespace Xingo\IDServer\Entities;

class Invoice extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'order' => Order::class,
    ];
}
