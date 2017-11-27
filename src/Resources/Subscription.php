<?php

namespace Xingo\IDServer\Resources;

use Illuminate\Support\Collection;
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
    public function all(int $page = 1, int $per_page = 10)
    {
        $query = http_build_query(compact('page', 'per_page'));

        $this->call('GET', "subscriptions?$query");

        return collect($this->contents['data'])->map(function ($data) {
            return $this->makeEntity($data);
        });
    }
}
