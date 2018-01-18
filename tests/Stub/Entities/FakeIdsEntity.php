<?php

namespace Tests\Stub\Entities;

use Xingo\IDServer\Contracts\IdsEntity;

class FakeIdsEntity implements IdsEntity
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param array $attributes
     * @param bool  $sync
     *
     * @return mixed
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }
}
