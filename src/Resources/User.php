<?php

namespace Xingo\IDServer\Resources;

use Intervention\Image\ImageManager;
use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

/**
 * Class User
 *
 * @package Xingo\IDServer\Resources
 * @property Tag tags
 * @property Address addresses
 */
class User extends Resource
{
    use ResourceBlueprint {
        get as protected getById;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @param array $claims
     * @return IdsEntity
     */
    public function login(string $email, string $password, bool $remember = false, array $claims = []): IdsEntity
    {
        $this->call('POST', 'auth/login', compact('email', 'password', 'remember', 'claims'));

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

        return $this->getById();
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

    /**
     * Get subscriptions for the user.
     *
     * @return Collection
     */
    public function communications()
    {
        $this->call('GET', "users/$this->id/communications");

        return $this->makeCollection(null, null, Entities\Communication::class);
    }

    /**
     * Get subscriptions for the user.
     *
     * @return Collection
     */
    public function subscriptions()
    {
        $this->call('GET', "users/$this->id/subscriptions");

        return $this->makeCollection(null, null, Entities\Subscription::class);
    }

    /**
     * @return Collection
     */
    public function abilities(): Collection
    {
        $this->call('GET', "users/$this->id/abilities");

        return $this->makeCollection(null, null, Entities\Ability::class);
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
                    $data,
                    Entities\Address::class
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
