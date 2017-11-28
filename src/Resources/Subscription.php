<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Entities;

/**
 * Class Subscription
 *
 * @package Xingo\IDServer\Resources
 */
class Subscription extends Resource
{
    /**
     * @param int $page
     * @param int $per_page
     * @return Collection
     */
    public function all(int $page = 1, int $per_page = 10): Collection
    {
        $query = compact('page', 'per_page');

        $this->call('GET', 'subscriptions', $query);

        return $this->makeCollection();
    }

    /**
     * @return Entities\Entity|Entities\Subscription
     */
    public function get(): Entities\Subscription
    {
        $this->call('GET', "subscriptions/$this->id");

        return $this->makeEntity();
    }

    /**
     * @param int $days
     * @return Collection
     */
    public function expiring(int $days = 7): Collection
    {
        $this->call('GET', 'subscriptions/expiring', compact('days'));

        return $this->makeCollection();
    }

    /**
     * @param array $attributes
     * @return Entities\Entity
     */
    public function create(array $attributes)
    {
        $attributes = array_only($attributes, ['store_id', 'plan_id', 'currency', 'coupon']);

        $this->call('POST', 'subscriptions', $attributes);

        return $this->makeEntity($this->contents['data'], Entities\Subscription::class, [
            'store' => Entities\Store::class,
            'user' => Entities\User::class,
            'plan' => Entities\Plan::class,
            'parent' => Entities\Subscription::class,
            'order' => Entities\Order::class,
        ]);
    }
}
