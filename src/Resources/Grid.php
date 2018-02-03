<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\FilteredQuery;

/**
 * Class Company
 *
 * @package Xingo\IDServer\Resources
 */
class Grid extends Resource
{
    use FilteredQuery;

    /**
     * Get all grid data.
     *
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function data(array $filters = []): \Illuminate\Support\Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', "grids/$this->id", $query);

        return $this->makeCollection();
    }

    /**
     * Export the grid data. This can be with or without filters.
     * It will return a callback printing the output of the stream.
     *
     * @param array $filters
     * @return \Closure
     */
    public function export(array $filters = [])
    {
        $body = $this->stream('GET', "grids/$this->id/export", $filters);

        return function () use ($body) {
            while (!$body->eof()) {
                echo $body->read(1024);
            }
        };
    }
}