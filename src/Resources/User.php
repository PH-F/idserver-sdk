<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Events\TokenRefreshed;

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
     *
     * @return IdsEntity
     */
    public function login(string $email, string $password, bool $remember = false, array $claims = [], $ip = ''): IdsEntity
    {
        $this->call('POST', 'auth/login', compact('email', 'password', 'remember', 'claims', 'ip'));

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

        event(new TokenRefreshed());
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
     *
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
     * @param mixed $avatar
     *
     * @return IdsEntity
     */
    public function changeAvatar($avatar): IdsEntity
    {
        $this->asMultipart()->call('PATCH', "users/{$this->id}/avatar", [
            'avatar' => $avatar,
        ]);

        return $this->makeEntity();
    }

    /**
     * Get communications for the user.
     *
     * @return Collection
     */
    public function communications()
    {
        $this->call('GET', "users/$this->id/communications");

        return $this->makeCollection(null, null, Entities\Communication::class);
    }

    /**
     * Get notes for the user.
     *
     * @return Collection
     */
    public function notes()
    {
        $this->call('GET', "users/$this->id/notes");

        return $this->makeCollection(null, null, Entities\Note::class);
    }

    /**
     * Get purchases for the user.
     *
     * @return Collection
     */
    public function subscriptions()
    {
        $this->call('GET', "users/$this->id/subscriptions");

        return $this->makeCollection(null, null, Entities\Subscription::class);
    }

    /**
     * Get subscriptions for the user.
     *
     * @return Collection
     */
    public function purchases()
    {
        $this->call('GET', "users/$this->id/purchases");

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
     * @param array $filters
     * @return Collection
     */
    public function orders(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', "users/$this->id/orders", $query);

        return $this->makeCollection(null, null, Entities\Order::class);
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
     *
     * @return string
     */
    public function forgotPassword($identifier): string
    {
        $field = $this->userIdentifierField($identifier);

        $this->call('POST', 'users/forgot-password', [
            $field => $identifier,
        ]);

        return $this->contents['token'];
    }

    /**
     * @param int|string $identifier
     * @param string $token
     * @param string $password
     *
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
     *
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
     * Reset the password of a user. This will reset the password and will
     * force the user to change his password after his first login. We
     * will send an email with the new password to the user.
     *
     * @return bool
     */
    public function resetPassword(): bool
    {
        $response = $this->call('PATCH', "users/{$this->id}/reset-password");

        return 204 === $response->getStatusCode();
    }

    /**
     * Retreives Shopify multipass token.
     *
     * @return string
     */
    public function multipass(string $ip = null): bool
    {
        $this->call('POST', "users/{$this->id}/multipass", [
            'ip' => $ip
        ]);

        return $this->contents['token'];
    }

    /**
     * Import users into the idserver.
     *
     * @param $data
     *
     * @return IdsEntity
     */
    public function import($data): IdsEntity
    {
        $this->asMultipart()->call('POST', 'users/import', $data);

        return $this->makeEntity(null, Entities\Import::class);
    }

    /**
     * @param array $filters
     * @return Collection
     */
    public function reportConfigurations(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', "users/$this->id/reports", $query);

        return $this->makeCollection(null, null, Entities\User\Report::class);
    }

    /**
     * @param  string  $type
     * @param  array  $data
     *
     * @return Collection
     */
    public function updateReportConfigurations(string $type, array $data = []): Collection
    {
        $this->call('PUT', "users/$this->id/reports/".$type, $data);

        return $this->makeCollection(null, null, Entities\User\Report::class);
    }

    /**
     * @param int|string $identifier
     *
     * @return string
     */
    private function userIdentifierField($identifier): string
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = validator([$identifier], ['email']);

        return $validator->passes() ? 'email' : 'id';
    }
}
