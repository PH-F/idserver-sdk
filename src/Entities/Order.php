<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities\Order\Item;
use Xingo\IDServer\Entities\Traits\Priceable;

class Order extends Entity
{
    use Priceable;

    /**
     * @var array
     */
    protected $relationships = [
        'items' => Item::class,
        'user' => User::class,
        'coupon' => Coupon::class,
    ];

    /**
     * @var array
     */
    protected $dates = [
        'paid_at',
    ];
}
