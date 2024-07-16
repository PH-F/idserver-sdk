<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Entities;
use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class Promotion
 *
 * @package Xingo\IDServer\Resources
 */
class Promotion extends Resource
{
    use ResourceBlueprint;

    /**
     * Get users that have the plan. By default it will send the users that currently have
     * an active subscription. It's also possible to send a date for which you want to
     * have the results.`
     *
     * @param  array  $filters
     *
     * @return Collection
     */
    public function loyaltyCoupons(array $filters = [])
    {
        $query = $this->queryString($filters);

        $this->call('GET', "promotions/loyalty", $query);

        return $this->makeCollection(null, null, \Xingo\IDServer\Entities\Promotion::class);
    }

    /**
     * Creates promotional code and links it to a user.
     *
     * @param integer $promotionId
     * @return void
     */
    public function redeem(int $promotionId)
    {
        $this->call('GET', "promotions/" . $promotionId . "/redeem");

        return $this->makeEntity(
            $this->contents['data'],
            Entities\Coupon::class
        );
    }
}
