<?php

namespace Xingo\IDServer;

use Illuminate\Support\Str;
use ReflectionClass;
use Xingo\IDServer\Contracts\IdsEntity;
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
     * @return IdsEntity
     */
    public function entity(array $attributes, ?string $class = null): IdsEntity
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
     * @param null|string $class
     * @return Collection
     */
    public function collection(array $data, array $meta, ?string $class = null): Collection
    {
        $items = collect($data)->map(function ($item) use ($class) {
            return $this->entity($item, $class);
        })->all();

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

        if ($relation && get_parent_class($relation) !== $class) {
            if (in_array(IdsEntity::class, class_implements($relation))) {
                $instance = new $relation();
                $instance->setRawAttributes($attributes);

                return $instance;
            }

            throw new \DomainException(
                'Custom entity classes must extend the original one or implement IdsEntity interface'
            );
        }

        return $relation ?
            new $relation($attributes) :
            new $class($attributes);
    }
}
