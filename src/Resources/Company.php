<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
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
     * @return Collection
     */
    public function addresses(): Collection
    {
        $this->call('GET', "companies/$this->id/addresses");

        return (new Collection($this->contents['data']))
            ->map(function ($data) {
                return $this->makeEntity(
                    $data,
                    Entities\Address::class
                );
            });
    }
}
