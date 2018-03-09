<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\FilteredQuery;
use Xingo\IDServer\Concerns\NestedResource;

class Tag extends Resource
{
    use NestedResource;
    use FilteredQuery;

    /**
     * @param array|string $tags
     * @return Collection
     */
    public function create($tags)
    {
        $this->call('POST', "users/{$this->parent->id}/tags", [
            'tags' => $tags,
        ]);

        return $this->makeCollection();
    }

    /**
     * @param array|string $tags
     * @return Collection
     */
    public function update($tags)
    {
        $this->call('PUT', "users/{$this->parent->id}/tags", [
            'tags' => $tags,
        ]);

        return $this->makeCollection();
    }

    /**
     * @param array $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $uri = is_null($this->parent) ? 'tags' : "users/{$this->parent->id}/tags";

        $this->call('GET', $uri, $query);

        return $this->makeCollection();
    }
}
