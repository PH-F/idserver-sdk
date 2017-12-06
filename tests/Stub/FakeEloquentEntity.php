<?php

namespace Tests\Stub;

use Xingo\IDServer\Contracts\EloquentEntity;

class FakeEloquentEntity implements EloquentEntity
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param array $attributes
     * @param bool $sync
     * @return mixed
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }
}
