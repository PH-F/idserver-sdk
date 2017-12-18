<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

class Address extends Resource
{
    use NestedResource;

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $query = $this->organizerQuery();

        $this->call('GET', 'addresses', $query);

        return $this->makeCollection();
    }

    /**
     * @return IdsEntity
     */
    public function get(): IdsEntity
    {
        $this->call('GET', "addresses/$this->id");

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Entities\Address
     */
    public function update(array $attributes): IdsEntity
    {
        $this->call('PUT', "addresses/$this->id", $attributes);

        return $this->makeEntity();
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $response = $this->call('DELETE', "addresses/$this->id");

        return 204 === $response->getStatusCode();
    }

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function create(array $attributes): IdsEntity
    {
        $resource = $this->toShortName($this->parent);
        $uri = "$resource/{$this->parent->id}/addresses";

        $this->call('POST', $uri, $attributes);

        return $this->makeEntity(
            $this->contents['data'], Entities\Address::class
        );
    }
}
