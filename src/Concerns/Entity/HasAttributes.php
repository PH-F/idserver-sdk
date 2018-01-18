<?php

namespace Xingo\IDServer\Concerns\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasAttributes as BaseHasAttributes;

trait HasAttributes
{
    use BaseHasAttributes {
        asDateTime as protected baseAsDateTime;
    }

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @param string $key
     * @return null
     */
    public function getRelationValue($key)
    {
        return null;
    }

    /**
     * @return bool
     */
    public function getIncrementing()
    {
        return $this->incrementing;
    }

    /**
     * @param mixed $value
     * @return Carbon
     */
    protected function asDateTime($value)
    {
        if (is_array($value)) {
            return Carbon::parse(array_get($value, 'date'))
                ->timezone(array_get($value, 'timezone'));
        }

        return $this->baseAsDateTime($value);
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
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        $this->setAttribute($name, $value);
    }
}
