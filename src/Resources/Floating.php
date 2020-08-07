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
}
