<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Entities;

class Subscription extends Entity
{
    /**
     * @var array
     */
    protected static $relations = [
        'store'  => Entities\Store::class,
        'user'   => Entities\User::class,
        'plan'   => Entities\Plan::class,
        'parent' => Entities\Subscription::class,
        'order'  => Entities\Order::class,
    ];

    /**
     * @var array
     */
    protected static $dates = [
        'start_date',
        'end_date',
    ];
}
