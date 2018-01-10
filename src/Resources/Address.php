<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

class Address extends Resource
{
    use NestedResource;
    use ResourceBlueprint;

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
