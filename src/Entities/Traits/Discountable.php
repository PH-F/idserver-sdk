<?php

namespace Xingo\IDServer\Entities\Traits;

trait Discountable
{
    /**
     * Get the given field as discount for humans.
     *
     * @param string|int $field
     *
     * @return string
     */
    public function asDiscountForHumans($field)
    {
        if (!is_int($field)) {
            $field = $this->$field;
        }

        if (is_null($field) || !is_numeric($field)) {
            return null;
        }

        return round($field / 100, 2) . '%';
    }
}
