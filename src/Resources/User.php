<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Entities\User as UserEntity;

class User extends Resource
{
    public function login(string $email, string $password)
    {
        $response = $this->call(
            'POST', '/auth/login', compact($email, $password)
        );

//        if ($response['status'])
    }

    /**
     * @param array $attributes
     * @return Entity|UserEntity
     */
    public function create(array $attributes): UserEntity
    {
        $this->call('POST', '/users', [
            'form_params' => [$attributes],
        ]);

        return $this->entity();
    }
}
