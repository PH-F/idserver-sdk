<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities;
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
     * Update setMandate information of the order.
     *
     * @param array $attributes
     * @return \Xingo\IDServer\Contracts\IdsEntity
     */
    public function setMandate(array $attributes)
    {
        $this->call('PATCH', "orders/$this->id/setMandate", $attributes);

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
     * @param string $orderNr
     * @param string $invoiceNr
     * @param string $email
     * @return \Xingo\IDServer\Contracts\IdsEntity
     * @throws \Xingo\IDServer\Exceptions\AuthorizationException
     * @throws \Xingo\IDServer\Exceptions\ForbiddenException
     * @throws \Xingo\IDServer\Exceptions\NotFoundException
     * @throws \Xingo\IDServer\Exceptions\ServerException
     * @throws \Xingo\IDServer\Exceptions\ThrottleException
     * @throws \Xingo\IDServer\Exceptions\ValidationException
     */
    public function recover(string $orderNr, string $invoiceNr, string $email)
    {
        $this->call('GET', "orders-recover/$orderNr/$invoiceNr/$email");

        return $this->makeEntity();
    }

    /**
     * @param string $orderNr
     * @param string $invoiceNr
     * @param string $email
     * @return \Xingo\IDServer\Contracts\IdsEntity
     * @throws \Xingo\IDServer\Exceptions\AuthorizationException
     * @throws \Xingo\IDServer\Exceptions\ForbiddenException
     * @throws \Xingo\IDServer\Exceptions\NotFoundException
     * @throws \Xingo\IDServer\Exceptions\ServerException
     * @throws \Xingo\IDServer\Exceptions\ThrottleException
     * @throws \Xingo\IDServer\Exceptions\ValidationException
     */
    public function recurring()
    {
        $this->call('GET', "recurring");

        return $this->makeEntity();
    }

    /**
     * Get notes for the order.
     *
     * @return Collection
     */
    public function notes()
    {
        $this->call('GET', "orders/$this->id/notes");

        return $this->makeCollection(null, null, Entities\Note::class);
    }
}
