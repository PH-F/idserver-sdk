<?php

namespace Xingo\IDServer\Resources;

use Illuminate\Support\Collection;
use Xingo\IDServer\Concerns\FilteredQuery;

class Floating extends Resource
{
    use FilteredQuery;

    /**
     * @param  array  $filters
     *
     * @return Collection
     */
    public function store(array $filters = []): Collection
    {
        $this->call('POST', "floatings/$this->id", [
            'filter' => $filters
        ]);

        return $this->makeCollection();
    }

    /**
     * Status of the running floating job, e.g. ids()->floatings->status().
     *
     * @return array ['running' => bool, 'type', 'user', 'started_at', 'processed', 'total']
     */
    public function status(): array
    {
        $this->call('GET', 'floatings/status');

        return $this->contents['data'] ?? ['running' => false];
    }
}
