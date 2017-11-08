<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Client;
use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Entities\User as UserEntity;

class User extends Resource
{
    /**
     * @param string $email
     * @param string $password
     * @return Entity
     */
    public function login(string $email, string $password)
    {
        $this->call('POST', '/auth/login', compact($email, $password));

        app(Client::class)->setToken($this->contents['token']);

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Entity|UserEntity
     */
    public function create(array $attributes): UserEntity
    {
        $this->call('POST', '/users', ['form_params' => [$attributes]]);

        return $this->makeEntity();
    }
}
