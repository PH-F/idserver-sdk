<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use Xingo\IDServer\Concerns\CallableResources;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources;

/**
 * Class Client
 *
 * @property Resources\Company companies
 * @method Resources\Company companies(int | Entities\Company $resource)
 * @property Resources\Subscription subscriptions
 * @method Resources\Subscription subscriptions(int | Entities\Subscription $resource)
 * @property Resources\User users
 * @method Resources\User users(int | Entities\User $resource)
 */
class Manager
{
    use CallableResources;

    /**
     * The name of the token in the session.
     */
    const TOKEN_NAME = 'jwt_token';

    /**
     * @var Client
     */
    protected $client;

    /**
     * Manager constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Get the JWT token which is used to connect to the IDServer
     *
     * @return string
     */
    public function getToken(): string
    {
        return (string)session(self::TOKEN_NAME);
    }

    /**
     * Set the given JWT token as token to use for connection to the IDServer.
     *
     * @param string $token
     * @return $this
     */
    public function setToken(string $token)
    {
        session()->put(self::TOKEN_NAME, $token);

        return $this;
    }

    /**
     * Remove JWT from the session.
     *
     * @return $this
     */
    public function removeToken()
    {
        session()->forget(self::TOKEN_NAME);

        return $this;
    }
}
