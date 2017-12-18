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

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function name()
    {
        $names = array_map('trim', [
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', array_filter($names));
    }
}
