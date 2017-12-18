<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

/**
 * Class Store
 *
 * @package Xingo\IDServer\Resources
 */
class Store extends Resource
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $query = $this->organizerQuery();

        $this->call('GET', 'stores', $query);

        return $this->makeCollection();
    }

    /**
     * @return IdsEntity
     */
    public function get(): IdsEntity
    {
        $this->call('GET', "stores/$this->id");

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function create(array $attributes): IdsEntity
    {
        $this->call('POST', 'stores', $attributes);

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function update(array $attributes): IdsEntity
    {
        $this->call('PUT', "stores/$this->id", $attributes);

        return $this->makeEntity();
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $response = $this->call('DELETE', "stores/$this->id");

        return 204 === $response->getStatusCode();
    }
}
