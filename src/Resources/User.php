<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Entities\User as UserEntity;

class User extends Resource
{
    /**
     * @param array $attributes
     * @return Entity|UserEntity
     */
    public function create(array $attributes): UserEntity
    {
        $response = $this->call('POST', '/users', [
            'form_params' => [$attributes],
        ]);

        if ($response['status'] === 201) {
            return $this->makeEntity($response['data']);
        }
    }
}
