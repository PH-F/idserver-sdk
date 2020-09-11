<?php

namespace Xingo\IDServer\Entities;

class Download extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'order' => Order::class,
    ];
}
