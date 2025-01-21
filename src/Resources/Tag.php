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
        $entity = $this->getEntityType($this->parent);

        $this->call('POST', "{$entity}/{$this->parent->id}/tags", [
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
        $entity = $this->getEntityType($this->parent);

        $this->call('PUT', "{$entity}/{$this->parent->id}/tags", [
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
        $entity = $this->getEntityType($this->parent);

        $query = $this->queryString($filters);

        $uri = is_null($this->parent) ? 'tags' : "{$entity}/{$this->parent->id}/tags";

        $this->call('GET', $uri, $query);

        return $this->makeCollection();
    }

    private function getEntityType($object)
    {
        if($object === null || !is_object($object)) {
            return 'users';
        }

        $class = get_class($object);
        if (strstr($class, 'Publisher')) {
            return 'publishers';
        }

        return 'users';
    }
}
