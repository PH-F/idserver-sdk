<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\FilteredQuery;
use Xingo\IDServer\Concerns\NestedResource;

/**
 * Class Duration
 *
 * @package Xingo\IDServer\Resources
 */
class Duration extends Resource
{
    use NestedResource;
    use FilteredQuery;

    /**
     * Get the custom resource name of this entity.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'plans-durations';
    }

    /**
     * @param array $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', 'plans-durations', $query);

        return $this->makeCollection();
    }
}
