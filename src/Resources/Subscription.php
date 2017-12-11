<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Entities;

/**
 * Class Subscription.
 */
class Subscription extends Resource
{
    /**
     * @param int $page
     * @param int $per_page
     *
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
     *
     * @return Collection
     */
    public function expiring(int $days = 7): Collection
    {
        $this->call('GET', 'subscriptions/expiring', compact('days'));

        return $this->makeCollection();
    }

    /**
     * @param array $attributes
     *
     * @return Entities\Entity|Entities\Subscription
     */
    public function create(array $attributes): Entities\Subscription
    {
        $attributes = array_only($attributes, ['store_id', 'plan_id', 'currency', 'coupon']);

        $this->call('POST', 'subscriptions', $attributes);

        return $this->makeEntity();
    }

    /**
     * @param Entities\Plan|int $plan
     *
     * @return Entities\Entity|Entities\Subscription
     */
    public function renew($plan): Entities\Subscription
    {
        $plan_id = $plan instanceof Entities\Plan ?
            $plan->id : (int) $plan;

        $this->call('POST', "subscriptions/$this->id/renew", compact('plan_id'));

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     *
     * @return Entities\Entity|Entities\Subscription
     */
    public function update(array $attributes): Entities\Subscription
    {
        $attributes = array_only($attributes, ['store_id', 'plan_id']);

        $this->call('PUT', "subscriptions/$this->id", $attributes);

        return $this->makeEntity();
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $response = $this->call('DELETE', "subscriptions/$this->id");

        return 204 === $response->getStatusCode();
    }
}
