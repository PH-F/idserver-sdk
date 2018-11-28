<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class Group
 *
 * @package Xingo\IDServer\Resources
 */
class Group extends Resource
{
    use ResourceBlueprint;

    /**
     * @return IdsEntity|Collection
     */
    public function get()
    {
        $this->call('GET', "discounts-groups/{$this->id}");

        return $this->makeCollection();
    }

    /**
     * @param array $attributes
     * @return Collection
     */
    public function update(array $attributes): IdsEntity
    {
        $this->call('PUT', "discounts-group/$this->id", $attributes);

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Collection
     */
    public function create(array $attributes)
    {
        $this->call('POST', "discounts-groups", $attributes);

        return $this->makeEntity();
    }
}
