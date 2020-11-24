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
     * Create a new invoice after the previous credit.
     *
     * @param array $attributes
     * @return \Xingo\IDServer\Contracts\IdsEntity
     */
    public function reinvoice(array $attributes)
    {
        $this->call('PATCH', "orders/$this->id/reinvoice", $attributes);

        return $this->makeEntity();
    }

    /**
     * Create a new invoice after the previous credit.
     *
     * @param array $attributes
     * @return \Xingo\IDServer\Contracts\IdsEntity
     */
    public function credit(array $attributes)
    {
        $this->call('PATCH', "orders/$this->id/credit", $attributes);

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


    /**
     * Find order bij ordernr and invoicenr
     *
     * @param array $attributes
     * @return Order
     */
    public function find(string $orderNr, string $invoiceNr)
    {
        $this->call('GET', "orders-find/$orderNr/$invoiceNr");

        return $this->makeEntity();
    }
}
