<?php

namespace Xingo\IDServer\Entities\Traits;

use NumberFormatter;

trait Priceable
{
    /**
     * Get the given field as price for humans.
     *
     * @param string|int $field
     * @param string $currency
     * @return string
     */
    public function asPriceForHumans($field, $currency)
    {
        if (!is_int($field)) {
            $field = $this->$field;
        }

        if (is_array($field)) {
            $field = array_get($field, $currency);
        }

        if (is_null($field)) {
            return null;
        }

        $formatter = new NumberFormatter(app('idserver.manager')->getLocale(), NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($field / 100, $currency);
    }
}
