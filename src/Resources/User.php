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
        $response = $this->call(
            'POST', '/auth/login', compact($email, $password)
        );

        if ($response->getStatusCode() === 200) {
            session()->put('jwt_token', $this->contents['token']);

            return $this->makeEntity();
        }

        // TODO 401 and 403
    }

    /**
     * @param array $attributes
     * @return Entity|UserEntity
     */
    public function create(array $attributes): UserEntity
    {
        $response = $this->call('POST', '/users', [
            'form_params' => [$attributes],
        ]);

        if ($response->getStatusCode() === 201) {
            return $this->makeEntity();
        }

        // TODO 403, 404, etc
    }
}
