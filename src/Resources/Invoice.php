<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class Invoice extends Resource
{
    use ResourceBlueprint {
        all as protected;
        get as protected;
        create as protected;
        delete as protected;
    }

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'invoices';
    }

    /**
     * @param array $filters
     * @return Collection
     */
    public function open(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $uri = 'invoices/open';

        $this->call('GET', $uri, $query);

        return $this->makeCollection();
    }
}
