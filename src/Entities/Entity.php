<?php

namespace Xingo\IDServer\Entities;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use JsonSerializable;
use Xingo\IDServer\Concerns\Entity\HasAttributes;
use Xingo\IDServer\Concerns\Entity\JsonArraySupport;
use Xingo\IDServer\Contracts\IdsEntity;

abstract class Entity implements ArrayAccess, Arrayable, IdsEntity, Jsonable, JsonSerializable
{
    use HasAttributes,
        HasTimestamps,
        JsonArraySupport;

    protected const DATE_FORMAT = 'Y-m-d H:i:s';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @var array
     */
    protected $relationships = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    protected function getDateFormat()
    {
        return $this->dateFormat ?: static::DATE_FORMAT;
    }
}
