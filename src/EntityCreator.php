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
     * @var array
     */
    protected $classes = [];

    /**
     * @param string $caller
     */
    public function __construct($caller)
    {
        $this->caller = $caller;
        $this->classes = config('idserver.classes');
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
        $relation = array_get($this->classes, $class);
        $instance = new $class();

        if ($relation) {
            if (!in_array(IdsEntity::class, class_implements($relation))) {
                throw new \DomainException(
                    'Custom entity classes must extend the original one or implement IdsEntity interface'
                );
            }

            $instance = new $relation();
        }

        $instance->setRawAttributes($attributes);
        $this->fillRelations($instance, $class, $attributes);

        return $instance;
    }

    /**
     * @param $instance
     * @param string $class
     * @param array $attributes
     */
    protected function fillRelations($instance, string $class, array $attributes)
    {
        $reflection = new \ReflectionProperty($class, 'relationships');
        $reflection->setAccessible(true);
        $relations = $reflection->getValue(new $class());

        collect($attributes)->each(function ($data, $name) use ($instance, $relations) {
            if (is_array($data) && array_key_exists($name, $relations)) {
                $instance->{$name} = $this->createRelation($name, $data, $relations);
            }
        });
    }

    /**
     * @param string $name
     * @param $data
     * @param array $relations
     * @return mixed
     */
    private function createRelation(string $name, $data, array $relations)
    {
        $class = array_get($relations, $name);
        $class = array_get($this->classes, $class, $class);

        if ($name === str_plural($name)) {
            $collection = new Collection($data);

            return $collection->map(function ($item) use ($class) {
                return $this->entity($item, $class);
            });
        }

        return $this->entity($data, $class);
    }
}
