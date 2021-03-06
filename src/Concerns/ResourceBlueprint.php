<?php

namespace Xingo\IDServer\Concerns;

use Illuminate\Support\Str;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Resources\Collection;

trait ResourceBlueprint
{
    use FilteredQuery;

    /**
     * @param array $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', $this->getResourceName(), $query);

        return $this->makeCollection();
    }

    /**
     * @return IdsEntity
     */
    public function get(): IdsEntity
    {
        $this->call('GET', $this->getResourceName() . "/$this->id");

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function create(array $attributes): IdsEntity
    {
        $this->call('POST', $this->getResourceName(), $attributes);

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function update(array $attributes): IdsEntity
    {
        $this->call('PUT', $this->getResourceName() . "/$this->id", $attributes);

        return $this->makeEntity();
    }

    /**
     * @param array|null $attributes
     * @return bool
     */
    public function delete(array $attributes = []): bool
    {
        $response = $this->call('DELETE', $this->getResourceName() . "/$this->id", $attributes);

        return 204 === $response->getStatusCode();
    }

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return strtolower(Str::plural(class_basename(static::class)));
    }

    /**
     * Download the data.
     *
     * @return \Closure
     */
    public function downloadStream()
    {
        $body = $this->stream('GET', $this->getResourceName() . "/$this->id");

        return function () use ($body) {
            while (!$body->eof()) {
                echo $body->read(1024);
            }
        };
    }
}
