<?php

namespace Xingo\IDServer\Entities\Traits;

use Illuminate\Support\Arr;
use NumberFormatter;

trait Priceable
{
    /**
     * Get the given field as price for humans.
     *
     * @param string|int $field
     * @param string $currency
     *
     * @return string
     */
    public function asPriceForHumans($field, $currency = null)
    {
        if (!is_int($field)) {
            $field = $this->$field;
        }

        if (is_array($field)) {
            $field = Arr::get($field, $currency);
        }

        if (is_null($currency)) {
            $currency = $this->getCurrencyValue();
        }

        if (is_null($field) || is_null($currency)) {
            return null;
        }

        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($field / 100, $currency);
    }

    /**
     * Get the currency value of the entity.
     *
     * @return string
     */
    public function getCurrencyValue()
    {
        return Arr::get((array)$this->currency, 'abbreviation');
    }

    /**
     * Get the symbol of the connected or given currency.
     *
     * @param string|null $currency
     *
     * @return string
     */
    public function getCurrencySymbol($currency = null)
    {
        if (is_null($currency)) {
            $currency = $this->getCurrencyValue();
        }

        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);

        $value = $formatter->formatCurrency('0', $currency);

        return trim(preg_replace('#[0-9.,\h]*#iu', '', $value));
    }
}
