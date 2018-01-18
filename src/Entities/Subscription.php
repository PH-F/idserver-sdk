<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities;

class Subscription extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'store' => Entities\Store::class,
        'user' => Entities\User::class,
        'plan' => Entities\Plan::class,
        'parent' => Entities\Subscription::class,
        'order' => Entities\Order::class,
    ];

    /**
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
    ];
}
