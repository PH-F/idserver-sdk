<?php

namespace Xingo\IDServer\Resources;

use Intervention\Image\ImageManager;
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
}
