<?php

namespace Xingo\IDServer\Contracts;

interface EloquentEntity
{
    /**
     * @param array $attributes
     * @param bool $sync
     * @return mixed
     */
    public function setRawAttributes(array $attributes, $sync = false);

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key);
}
