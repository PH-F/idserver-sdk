<?php

namespace Xingo\IDServer\Resources;

use Intervention\Image\ImageManager;
use Xingo\IDServer\Entities;

/**
 * Class User
 *
 * @package Xingo\IDServer\Resources
 * @property Tag tags
 * @property \Xingo\IDServer\Resources\Address addresses
 */
class User extends Resource
{
    /**
     * @param string $email
     * @param string $password
     * @return Entities\Entity
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

        $header = $response->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        app('idserver.manager')->setToken($token);
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return Collection
     */
    public function all(int $page = 1, int $per_page = 10): Collection
    {
        $query = compact('page', 'per_page');

        $this->call('GET', 'users', $query);

        return $this->makeCollection();
    }

    /**
     * @param array $attributes
     * @return Entities\Entity|Entities\User
     */
    public function create(array $attributes): Entities\User
    {
        $this->call('POST', 'users', $attributes);

        return $this->makeEntity();
    }

    /**
     * @return Entities\Entity|Entities\User|Collection
     */
    public function get()
    {
        if (is_array($this->id) && count($this->id) > 1) {
            $this->call('GET', 'users', [
                'ids' => implode(',', $this->id),
            ]);

            return $this->makeCollection();
        }

        $this->call('GET', sprintf('users/%d', $this->id));

        return $this->makeEntity();
    }

    /**
     * @param array $attributes
     * @return Entities\Entity|Entities\User
     */
    public function update(array $attributes = []): Entities\User
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
     * @return Entities\Entity
     */
    public function confirm(string $token): Entities\Entity
    {
        $this->call('PATCH', "users/{$this->id}/confirm", [
            'token' => $token,
        ]);

        return $this->makeEntity();
    }

    /**
     * @param string $avatar
     * @return Entities\Entity
     */
    public function changeAvatar(string $avatar): Entities\Entity
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
     * @return Entities\Entity|Entities\User
     */
    public function tags(): Entities\User
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

        return (new Collection($this->contents['data']))
            ->map(function ($data) {
                return $this->makeEntity(
                    $data, Entities\Address::class
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
    public function updatePassword(string $token, string $password): bool
    {
        $response = $this->call('PATCH', "users/{$this->id}/update-password", [
            'token' => $token,
            'password' => $password,
        ]);

        return 204 === $response->getStatusCode();
    }

    /**
     * @param string $password
     * @return bool
     */
    public function changePassword(string $password): bool
    {
        $response = $this->call('PATCH', "users/{$this->id}/change-password", [
            'password' => $password,
        ]);

        return 204 === $response->getStatusCode();
    }
}
