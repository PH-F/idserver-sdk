<?php

namespace Xingo\IDServer\Entities;

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
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $this->parseRelations($attributes);
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
    protected function parseRelations(array $attributes): array
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
}
