<?php

namespace Xingo\IDServer\Resources;

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
        $this->call('POST', 'auth/login', compact('email', 'password'));

        app('idserver.manager')->setToken($this->contents['token']);

        return $this->makeEntity();
    }

    /**
     * @return void
     */
    public function refreshToken()
    {
        $response = $this->call('PUT', 'auth/refresh');

        $header = $response->getHeaderLine('Authentication');
        $token = str_replace('Bearer ', '', $header);
        app('idserver.manager')->setToken($token);
    }

    /**
     * @param array $attributes
     * @return Entity|UserEntity
     */
    public function create(array $attributes): UserEntity
    {
        $this->call('POST', 'users', ['form_params' => [$attributes]]);

        return $this->makeEntity();
    }

    /**
     * @param int $id
     * @return Entity|UserEntity
     */
    public function get(?int $id = null): UserEntity
    {
        $this->call('GET', 'users/' . $id ?: $this->id);

        return $this->makeEntity();
    }

    /**
     * @param int|array $id
     * @param array $attributes
     * @return Entity|UserEntity
     */
    public function update($id, array $attributes = []): UserEntity
    {
        if (is_array($id) && !empty($id)) {
            $attributes = $id;
            $id = $this->id;
        }

        $this->call('PUT', 'users/' . $id, $attributes);

        return $this->makeEntity();
    }
}
