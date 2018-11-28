<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\FilteredQuery;
use Xingo\IDServer\Concerns\NestedResource;

/**
 * Class Variant
 *
 * @package Xingo\IDServer\Resources
 */
class Variant extends Resource
{
    use NestedResource;
    use FilteredQuery;

    /**
     * @param array $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', 'plans-variants', $query);

        return $this->makeCollection();
    }
}
