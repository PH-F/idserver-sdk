<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;

/**
 * Class Role
 *
 * @package Xingo\IDServer\Resources
 * @property Ability abilities
 */
class Role extends Resource
{
    use ResourceBlueprint;

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
            $role->abilities = (array)$this->contents['data'];
        }

        return $role;
    }
}
