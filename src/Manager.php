<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

/**
 * Class Client
 *
 * @property \Xingo\IDServer\Resources\User users
 */
class Manager
{
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
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $resource = Str::studly(Str::singular($name));
        $class = "Xingo\\IDServer\\Resources\\$resource";

        if (class_exists($class)) {
            return app()->make($class, [
                'client' => $this->client
            ]);
        }
    }

    /**
     * Get the JWT token which is used to connect to the IDServer
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
     */
    public function setToken(string $token): void
    {
        session()->put(self::TOKEN_NAME, $token);
    }
}
