<?php

namespace Xingo\IDServer\Entities\Order;

use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Entities\Plan\Duration;
use Xingo\IDServer\Entities\Subscription;
use Xingo\IDServer\Entities\Traits\Priceable;

class Item extends Entity
{
    use Priceable;

    /**
     * @var array
     */
    protected $relationships = [
        'subscription' => Subscription::class,
        'parentSubscription' => Subscription::class,
        'plan_duration' => Duration::class,
    ];

    /**
     * Get the total price of this item.
     *
     * @return int
     */
    public function getTotalAttribute()
    {
        return $this->price - $this->discount - $this->shipping_cost;
    }
}
