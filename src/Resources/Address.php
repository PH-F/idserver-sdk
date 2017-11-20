<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Entities\Address as AddressEntity;
use Xingo\IDServer\Entities\Entity;

class Address extends Resource
{
    use NestedResource;

    /**
     * @param array $attributes
     * @return AddressEntity|Entity
     */
    public function create(array $attributes)
    {
        $this->call(
            'POST', "users/{$this->parent->id}/addresses", $attributes
        );

        return $this->makeEntity(
            $this->contents['data'], AddressEntity::class
        );
    }
}
