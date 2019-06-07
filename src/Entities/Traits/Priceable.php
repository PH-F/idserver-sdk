<?php

namespace Xingo\IDServer\Entities\Traits;

use Illuminate\Support\Arr;
use NumberFormatter;

trait Priceable
{
    /**
     * Get the given field as price for humans.
     *
     * @param  string|int  $field
     * @param  string  $currency
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

        return $this->toPrice($field, $currency);
    }

    /**
     * Map the given array of price fields to an array with the prices in human friendly format.
     *
     * @param  array  $fields
     *
     * @return array
     */
    public function mapToPriceForHumans(array $fields)
    {
        return collect($fields)
            ->map(function ($price, $currency) {
                return $this->toPrice($price, $currency);
            })
            ->all();
    }

    /**
     * Get the currency value of the entity.
     *
     * @return string
     */
    public function getCurrencyValue()
    {
        return Arr::get((array) $this->currency, 'abbreviation');
    }

    /**
     * Format the given price.
     *
     * @param  string  $amount
     * @param  string  $currency
     *
     * @return string
     */
    protected function toPrice($amount, $currency): string
    {
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($amount / 100, $currency);
    }

    /**
     * Get the symbol of the connected or given currency.
     *
     * @param  string|null  $currency
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
