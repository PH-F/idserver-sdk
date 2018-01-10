<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

/**
 * Class Company
 *
 * @package Xingo\IDServer\Resources
 * @property \Xingo\IDServer\Resources\Address addresses
 */
class Company extends Resource
{
    use ResourceBlueprint;

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function create(array $attributes): IdsEntity
    {
        $attributes = array_only($attributes, ['name', 'department', 'vat']);

        $this->call('POST', 'companies', $attributes);

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function update(array $attributes): IdsEntity
    {
        $attributes = array_only($attributes, ['name', 'department', 'vat']);

        $this->call('PUT', "companies/$this->id", $attributes);

        return $this->makeEntity();
    }

    /**
     * @return Collection
     */
    public function addresses(): Collection
    {
        $this->call('GET', "companies/$this->id/addresses");

        return (new Collection($this->contents['data']))
            ->map(function ($data) {
                return $this->makeEntity(
                    $data, Entities\Address::class
                );
            });
    }
}
