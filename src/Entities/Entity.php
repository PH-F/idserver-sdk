<?php

namespace Xingo\IDServer\Entities;

use Carbon\Carbon;

abstract class Entity
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected static $relations = [];

    /**
     * @var array
     */
    protected static $dates = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $this->convert($attributes);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function convert(array $attributes): array
    {
        return $this->convertDates(
            $this->convertRelations($attributes)
        );
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function convertDates(array $attributes): array
    {
        if (empty(static::$dates)) {
            return $attributes;
        }

        foreach (static::$dates as $field) {
            $value = array_get($attributes, $field);
            $carbon = $this->createCarbonInstance($value);
            array_set($attributes, $field, $carbon);
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function convertRelations(array $attributes): array
    {
        if (empty(static::$relations)) {
            return $attributes;
        }

        return collect($attributes)->map(function ($data, $name) {
            return is_array($data) && array_key_exists($name, static::$relations) ?
                new static::$relations[$name]($data) :
                $data;
        })->all();
    }

    /**
     * @param string|array $value
     * @return Carbon
     */
    private function createCarbonInstance($value): Carbon
    {
        if (!is_array($value)) {
            return new Carbon($value);
        }

        return new Carbon($value['date'], $value['timezone']);
    }
}
