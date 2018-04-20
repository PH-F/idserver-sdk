<?php

namespace Xingo\IDServer\Resources;

use Illuminate\Support\Collection;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class Report extends Resource
{
    /**
     * @param array $filters
     * @return Collection
     */
    public function get(array $filters = []): Collection
    {
        $this->call('GET', "reports/$this->id", $filters);

        return $this->makeCollection();
    }
}
