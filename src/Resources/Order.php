<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities\Order\Price;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class Order extends Resource
{
    use ResourceBlueprint;

    /**
     * Update payment information of the order.
     *
     * @param array $attributes
     * @return \Xingo\IDServer\Contracts\IdsEntity
     */
    public function payment(array $attributes)
    {
        $this->call('PATCH', "orders/$this->id/payment", $attributes);

        return $this->makeEntity();
    }

    /**
     * Get price information for a subscription.
     *
     * @param array $attributes
     * @return Price
     */
    public function price(array $attributes)
    {
        $this->call('GET', "orders-price", $attributes);

        return $this->makeEntity(null, Price::class);
    }
}
