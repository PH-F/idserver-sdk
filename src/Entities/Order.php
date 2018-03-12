<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities\Order\Item;
use Xingo\IDServer\Entities\Plan\Duration;

class Order extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'items' => Item::class,
        'user' => User::class,
        'plan_duration' => Duration::class,
    ];

    /**
     * @var array
     */
    protected $dates = [
        'paid_at',
    ];
}
