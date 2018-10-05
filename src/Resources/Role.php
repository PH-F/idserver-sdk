<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities\Ability;

/**
 * Class Role
 *
 * @package Xingo\IDServer\Resources
 * @property Ability abilities
 */
class Role extends Resource
{
    use ResourceBlueprint;
    use NestedResource;

    /**
     * @param array $attributes
     * @param array $abilities
     * @return IdsEntity
     */
    public function update(array $attributes, array $abilities = []): IdsEntity
    {
        $this->call('PUT', "roles/$this->id", $attributes);

        /** @var \Xingo\IDServer\Entities\Role $role */
        $role = $this->makeEntity();

        if (!empty($abilities)) {
            $this->call('PUT', "roles/$this->id/abilities", $abilities);
            $role->abilities = $this->makeCollection(null, null, Ability::class);
        }

        return $role;
    }

    /**
     * @param array|null $roles
     * @return Collection
     */
    public function sync(?array $roles)
    {
        $resource = $this->toShortName($this->parent);
        $uri = "$resource/{$this->parent->id}/roles";

        $this->call('PUT', $uri, ['roles' => $roles]);

        return $this->makeCollection();
    }
}
