<?php

namespace Xingo\IDServer\Entities;

use ArrayAccess;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
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

    protected const DATE_FORMAT = "Y-m-d\\TH:i:s.uP";

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
        $format = $this->dateFormat ?: static::DATE_FORMAT;

        // https://bugs.php.net/bug.php?id=75577
        if (version_compare(PHP_VERSION, '7.3.0-dev', '<')) {
            $format = str_replace('.v', '.u', $format);
        }

        return $format;
    }

    /**
     * Check if we're dealing with a deleted entity.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return isset($this['deleted_at']) && $this->deleted_at !== null;
    }
}
