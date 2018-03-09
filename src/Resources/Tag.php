<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\FilteredQuery;
use Xingo\IDServer\Concerns\NestedResource;

class Tag extends Resource
{
    use NestedResource;
    use FilteredQuery;

    /**
     * @param array|string $tag
     * @return Collection
     */
    public function create($tag)
    {
        $this->call('POST', "users/{$this->parent->id}/tags", [
            'tag' => $tag,
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
