<?php

namespace Xingo\IDServer\Entities;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\JsonEncodingException;
use JsonSerializable;
use Xingo\IDServer\Contracts\IdsEntity;

abstract class Entity implements Arrayable, IdsEntity, Jsonable, JsonSerializable
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
    protected static $dates = [
        'created_at',
        'updated_at',
    ];

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
        return $this->getAttribute($name);
    }

    /**
     * @param array $attributes
     * @param bool $sync
     * @return mixed|void
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $this->convert($attributes);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            } else {
                return $value;
            }
        }, $this->attributes);
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

        $dateFields = array_merge(static::$dates, self::$dates);

        foreach ($dateFields as $field) {
            if ($value = array_get($attributes, $field)) {
                $carbon = $this->createCarbonInstance($value);
                array_set($attributes, $field, $carbon);
            }
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
