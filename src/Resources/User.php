<?php

namespace Xingo\IDServer\Resources;

use Intervention\Image\ImageManager;
use Xingo\IDServer\Concerns\FilteredQuery;
use Xingo\IDServer\Contracts\IdsEntity;
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
    use FilteredQuery;

    /**
     * @param string $email
     * @param string $password
     * @param array $claims
     * @return IdsEntity
     */
    public function login(string $email, string $password, array $claims = []): IdsEntity
    {
        $this->call('POST', 'auth/login', compact('email', 'password', 'claims'));

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
     * @param array $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', 'users', $query);

        return $this->makeCollection();
    }

    /**
     * @param array $attributes
     * @return IdsEntity
     */
    public function create(array $attributes): IdsEntity
    {
        $this->call('POST', 'users', $attributes);

        return $this->makeEntity();
    }

    /**
     * @return IdsEntity|Collection
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
     * @return IdsEntity
     */
    public function update(array $attributes = []): IdsEntity
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
     * @return IdsEntity
     */
    public function confirm(string $token): IdsEntity
    {
        $this->call('PATCH', "users/{$this->id}/confirm", [
            'token' => $token,
        ]);

        return $this->makeEntity();
    }

    /**
     * @param string $avatar
     * @return IdsEntity
     */
    public function changeAvatar(string $avatar): IdsEntity
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
     * @return IdsEntity
     */
    public function tags(): IdsEntity
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
     * @param int|string $identifier
     * @return string
     */
    public function resetPassword($identifier): string
    {
        $field = $this->userIdentifierField($identifier);

        $this->call('POST', 'users/reset-password', [
            $field => $identifier,
        ]);

        return $this->contents['token'];
    }

    /**
     * @param int|string $identifier
     * @param string $token
     * @param string $password
     * @return bool
     */
    public function updatePassword($identifier, string $token, string $password): bool
    {
        $field = $this->userIdentifierField($identifier);

        $response = $this->call('PATCH', 'users/update-password', [
            $field => $identifier,
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

    /**
     * @param int|string $identifier
     * @return string
     */
    private function userIdentifierField($identifier): string
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = validator([$identifier], ['email']);

        return $validator->passes() ? 'email' : 'id';
    }
}
