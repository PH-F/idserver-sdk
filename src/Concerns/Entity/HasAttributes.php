<?php

namespace Xingo\IDServer\Concerns\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasAttributes as BaseHasAttributes;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Support\Arr;

trait HasAttributes
{
    use BaseHasAttributes {
        asDateTime as protected baseAsDateTime;
    }

    use HasRelationships;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @return bool
     */
    public function getIncrementing()
    {
        return $this->incrementing;
    }

    /**
     * @param mixed $value
     *
     * @return Carbon
     */
    protected function asDateTime($value)
    {
        if (is_array($value)) {
            return Carbon::parse(Arr::get($value, 'date'), Arr::get($value, 'timezone'))
                ->setTimezone(config('app.timezone'));
        }

        return $this->baseAsDateTime($value)->setTimezone(config('app.timezone'));
    }

    /**
     * @param string $name
     *
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

    /**
     * Get a relationship value from a method.
     *
     * @param  string $method
     *
     * @return mixed
     *
     * @throws \LogicException
     */
    protected function getRelationshipFromMethod($method)
    {
        $relation = $this->$method();

        return tap($relation, function ($results) use ($method) {
            $this->setRelation($method, $results);
        });
    }
}
