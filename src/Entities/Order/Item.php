<?php

namespace Xingo\IDServer\Entities\Order;

use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Entities\Subscription;

class Item extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'subscription' => Subscription::class,
        'parentSubscription' => Subscription::class,
    ];

    /**
     * Get the total price of this item.
     *
     * @return int
     */
    public function total()
    {
        return $this->price - $this->discount - $this->shipping_cost;
    }
}
