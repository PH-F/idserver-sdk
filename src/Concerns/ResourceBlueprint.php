<?php

namespace Xingo\IDServer\Concerns;

use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Resources\Collection;

trait ResourceBlueprint
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $query = $this->organizerQuery();

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
     * @return bool
     */
    public function delete(): bool
    {
        $response = $this->call('DELETE', $this->getResourceName() . "/$this->id");

        return 204 === $response->getStatusCode();
    }

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return strtolower(str_plural(class_basename(static::class)));
    }
}