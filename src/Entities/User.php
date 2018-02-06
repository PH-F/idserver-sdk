<?php

namespace Xingo\IDServer\Entities;

use Xingo\IDServer\Resources\Collection;

/**
 * Class User
 * @property Collection $abilities
 * @package Xingo\IDServer\Entities
 */
class User extends Entity
{
    /**
     * @var array
     */
    protected $dates = ['date_of_birth'];

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

    /**
     * Get all abilities of a user.
     *
     * @return Collection
     */
    public function abilities()
    {
        return ids()->users($this->id)->abilities();
    }

    /**
     * Check if the user can perform the given ability.
     *
     * @param string $ability
     * @return bool
     */
    public function hasAbility(string $ability)
    {
        $allowed = $this->abilities->pluck('name');

        return $allowed->contains($ability) || $allowed->contains('*');
    }
}
