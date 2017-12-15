<?php

namespace Xingo\IDServer\Resources;

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
        $query = $this->paginationQuery();

        $this->call('GET', 'stores', $query);

        return $this->makeCollection();
    }

    /**
     * @return Entities\Entity|Entities\Store
     */
    public function get(): Entities\Store
    {
        $this->call('GET', "stores/$this->id");

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Entities\Entity|Entities\Store
     */
    public function create(array $attributes): Entities\Store
    {
        $this->call('POST', 'stores', $attributes);

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Entities\Entity|Entities\Store
     */
    public function update(array $attributes): Entities\Store
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
