<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Entities;

/**
 * Class Company
 *
 * @package Xingo\IDServer\Resources
 * @property \Xingo\IDServer\Resources\Address addresses
 */
class Company extends Resource
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $query = $this->paginationQuery();

        $this->call('GET', 'companies', $query);

        return $this->makeCollection();
    }

    /**
     * @return Entities\Entity|Entities\Company
     */
    public function get(): Entities\Company
    {
        $this->call('GET', "companies/$this->id");

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Entities\Entity|Entities\Company
     */
    public function create(array $attributes): Entities\Company
    {
        $attributes = array_only($attributes, ['name', 'department', 'vat']);

        $this->call('POST', 'companies', $attributes);

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Entities\Entity|Entities\Company
     */
    public function update(array $attributes): Entities\Company
    {
        $attributes = array_only($attributes, ['name', 'department', 'vat']);

        $this->call('PUT', "companies/$this->id", $attributes);

        return $this->makeEntity();
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $response = $this->call('DELETE', "companies/$this->id");

        return 204 === $response->getStatusCode();
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
