<?php

namespace Xingo\IDServer\Resources;

use Illuminate\Support\Collection as BaseCollection;
use Xingo\IDServer\Entities;

/**
 * Class Company
 *
 * @package Xingo\IDServer\Resources
 */
class Company extends Resource
{
    /**
     * @param int $page
     * @param int $per_page
     * @return Collection
     */
    public function all(int $page = 1, int $per_page = 10): Collection
    {
        $query = compact('page', 'per_page');

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
     * @return BaseCollection
     */
    public function addresses(): BaseCollection
    {
        $this->call('GET', "companies/$this->id/addresses");

        return collect($this->contents['data'])
            ->map(function ($data) {
                return $this->makeEntity(
                    $data, Entities\Address::class
                );
            });
    }
}
