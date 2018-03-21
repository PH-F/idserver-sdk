<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

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
}
