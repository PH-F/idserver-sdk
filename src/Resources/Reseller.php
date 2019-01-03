<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities;

/**
 * Class Reseller
 *
 * @package Xingo\IDServer\Resources
 * @property \Xingo\IDServer\Resources\Address addresses
 */
class Reseller extends Resource
{
    use ResourceBlueprint;

    /**
     * @return Collection
     */
    public function addresses(): Collection
    {
        $this->call('GET', "resellers/$this->id/addresses");

        return (new Collection($this->contents['data']))
            ->map(function ($data) {
                return $this->makeEntity(
                    $data,
                    Entities\Address::class
                );
            });
    }

    /**
     * Get subscriptions for the user.
     *
     * @return Collection
     */
    public function communications()
    {
        $this->call('GET', "resellers/$this->id/communications");

        return $this->makeCollection(null, null, Entities\Communication::class);
    }
}
