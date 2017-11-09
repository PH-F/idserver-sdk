<?php

namespace Xingo\IDServer;

use Illuminate\Support\Str;

/**
 * Class Client
 *
 * @property \Xingo\IDServer\Resources\User users
 */
class Client
{
    /**
     * The name of the token in the session.
     */
    const TOKEN_NAME = 'jwt_token';

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
                app()->make(\GuzzleHttp\Client::class)
            ]);
        }
    }

    /**
     * Get the JWT token which is used to connect to the IDServer
     * @return string
     */
    public function getToken(): string
    {
        return (string) session(self::TOKEN_NAME);
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
        
        return $this
    }
}
