<?php

namespace Xingo\IDServer;

use Illuminate\Support\Str;
use ReflectionClass;
use Xingo\IDServer\Resources\Collection;

class EntityCreator
{
    /**
     * @var string
     */
    protected $caller;

    /**
     * @param string $caller
     */
    public function __construct($caller)
    {
        $this->caller = $caller;
    }

    /**
     * @param array $attributes
     * @param null|string $class
     * @return mixed
     */
    public function entity(array $attributes, ?string $class = null)
    {
        if ($class === null) {
            $entity = (new ReflectionClass($this->caller))->getShortName();
            $class = sprintf('Xingo\\IDServer\\Entities\\%s', Str::studly($entity));
        }

        return $this->createInstance($class, $attributes);
    }

    /**
     * @param array $data
     * @param array $meta
     * @return Collection
     */
    public function collection(array $data, array $meta): Collection
    {
        $items = collect($data)->map(function ($item) {
            return $this->entity($item);
        })->toArray();

        return new Collection($items, $meta);
    }

    /**
     * @param string $class
     * @param array $attributes
     * @return mixed
     */
    protected function createInstance(string $class, array $attributes)
    {
        $relation = array_get(config('idserver.classes'), $class);

        return $relation ?
            new $relation($attributes) :
            new $class($attributes);
    }
}
