<?php

namespace Xingo\IDServer\Entities;

class User extends Entity
{
    /**
     * @var array
     */
    protected static $dates = ['date_of_birth'];

    /**
     * @return string
     */
    public function jwtToken()
    {
        return session()->get('jwt_token');
    }
}
