<?php

namespace Xingo\IDServer\Resources;

use Intervention\Image\ImageManager;
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

    /**
     * @param int|null $id
     * @return bool
     */
    public function delete(?int $id = null): bool
    {
        $id = $id ?: $this->id;
        $response = $this->call('DELETE', "users/{$id}");

        return 204 === $response->getStatusCode();
    }

    /**
     * @param string $token
     * @return Entity
     */
    public function confirm(string $token): Entity
    {
        $this->call('PATCH', "users/{$this->id}/confirm", [
            'token' => $token,
        ]);

        return $this->makeEntity();
    }

    /**
     * @param string $avatar
     * @return Entity
     */
    public function changeAvatar(string $avatar): Entity
    {
        $this->call('PATCH', "users/{$this->id}/avatar", [
            'avatar' => base64_encode(
                (new ImageManager())->make($avatar)
            ),
        ]);

        return $this->makeEntity(array_merge(
            $this->contents['user'],
            ['avatar' => $this->contents['avatar']]
        ));
    }

    public function subscriptions()
    {
        // TODO
    }

    /**
     * @return Entity
     */
    public function tags(): Entity
    {
        $this->call('GET', "users/{$this->id}/tags");

        return $this->makeEntity(array_merge(
            $this->contents['user'],
            ['tags' => $this->contents['tags']]
        ));
    }

    public function addresses()
    {

    }

    /**
     * @return string
     */
    public function resetPassword(): string
    {
        $this->call('POST', "users/{$this->id}/reset-password");

        return $this->contents['token'];
    }

    public function changePassword()
    {

    }
}
