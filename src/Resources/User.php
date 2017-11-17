<?php

namespace Xingo\IDServer\Resources;

use Illuminate\Support\Collection;
use Intervention\Image\ImageManager;
use Xingo\IDServer\Entities\Address;
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
        $this->call('POST', 'users', $attributes);

        return $this->makeEntity();
    }

    /**
     * @return Entity|UserEntity
     */
    public function get(): UserEntity
    {
        $this->call('GET', sprintf("users/{$this->id}"));

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Entity|UserEntity
     */
    public function update(array $attributes = []): UserEntity
    {
        $this->call('PUT', "users/{$this->id}", $attributes);

        return $this->makeEntity();
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $response = $this->call('DELETE', "users/{$this->id}");

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
                    ->stream()
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

    /**
     * @return Collection
     */
    public function addresses(): Collection
    {
        $this->call('GET', "users/$this->id/addresses");

        return collect($this->contents['data'])
            ->map(function ($data) {
                return $this->makeEntity(
                    $data, Address::class
                );
            });
    }

    /**
     * @return string
     */
    public function resetPassword(): string
    {
        $this->call('POST', "users/{$this->id}/reset-password");

        return $this->contents['token'];
    }

    /**
     * @param string $token
     * @param string $password
     * @return bool
     */
    public function changePassword(string $token, string $password): bool
    {
        $response = $this->call('PATCH', "users/{$this->id}/change-password", [
            'token' => $token,
            'password' => $password,
        ]);

        return 204 === $response->getStatusCode();
    }
}
