<?php

namespace Xingo\IDServer\Entities;

use Carbon\Carbon;
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
        'original' => Entities\Subscription::class,
        'order' => Entities\Order::class,
    ];

    /**
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'extended_end_date',
    ];

    /**
     * Show the address types as badges.
     *
     * @return string
     */
    public function isActive()
    {
        if (!in_array($this->status, ['active', 'expiring'])) {
            return false;
        }

        return Carbon::now()->between($this->start_date, $this->extended_end_date ?? $this->end_date);
    }
}
