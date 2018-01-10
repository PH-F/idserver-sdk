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
     * @param Entities\Plan|int $plan
     * @return IdsEntity
     */
    public function renew($plan): IdsEntity
    {
        $plan_id = $plan instanceof Entities\Plan ?
            $plan->id : (int)$plan;

        $this->call('POST', "subscriptions/$this->id/renew", compact('plan_id'));

        return $this->makeEntity();
    }
}
