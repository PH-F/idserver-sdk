<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

/**
 * Class Subscription
 *
 * @package Xingo\IDServer\Resources
 */
class Subscription extends Resource
{
    use ResourceBlueprint;

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
     * @return IdsEntity
     */
    public function renew(array $attributes): IdsEntity
    {
        $this->call('POST', "subscriptions/$this->id/renew", $attributes);

        return $this->makeEntity(null, Entities\Order::class);
    }

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function cancel(array $attributes): IdsEntity
    {
        $this->call('PUT', "subscriptions/$this->id/cancel", $attributes);

        return $this->makeEntity(null, Entities\Order::class);
    }

    /**
     * @return IdsEntity
     */
    public function stopRecurring(): IdsEntity
    {
        $this->call('PUT', "subscriptions/$this->id/recurring/stop");

        return $this->makeEntity(null, Entities\Order::class);
    }
}
