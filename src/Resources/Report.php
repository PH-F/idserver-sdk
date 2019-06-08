<?php

namespace Xingo\IDServer\Resources;

use Illuminate\Support\Collection;
use Xingo\IDServer\Concerns\FilteredQuery;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class Report extends Resource
{
    use FilteredQuery;

    /**
     * @param  array  $filters
     *
     * @return Collection
     */
    public function get(array $filters = []): Collection
    {
        $this->call('GET', "reports/$this->id", [
            'filter' => $filters
        ]);

        return $this->makeCollection();
    }
}
