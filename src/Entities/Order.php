<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities\Order\Item;

class Order extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'items' => Item::class,
        'user' => User::class,
    ];

    /**
     * @var array
     */
    protected $dates = [
        'paid_at',
    ];
}
