<?php

namespace Xingo\IDServer\Entities;

class User extends Entity
{
    /**
     * @return string
     */
    public function jwtToken()
    {
        return session()->get('jwt_token');
    }
}
