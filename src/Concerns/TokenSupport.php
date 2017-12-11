<?php

namespace Xingo\IDServer\Concerns;

trait TokenSupport
{
    /**
     * The name of the token in the session.
     */
    private static $tokenName = 'jwt_token';

    /**
     * Get the JWT token which is used to connect to the IDServer
     *
     * @return string
     */
    public function getToken(): string
    {
        return (string)session(static::$tokenName);
    }

    /**
     * Set the given JWT token as token to use for connection to the IDServer.
     *
     * @param string $token
     * @return $this
     */
    public function setToken(string $token)
    {
        session()->put(static::$tokenName, $token);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasToken(): bool
    {
        return session()->has(static::$tokenName);
    }

    /**
     * Remove JWT from the session.
     *
     * @return $this
     */
    public function removeToken()
    {
        session()->forget(static::$tokenName);

        return $this;
    }
}
