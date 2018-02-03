<?php

namespace Xingo\IDServer\Concerns;

use Illuminate\Support\Str;
use Xingo\IDServer\Resources\Resource;

trait CallableResource
{
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $resource = Str::studly(Str::singular($name));
        $class = "Xingo\\IDServer\\Resources\\$resource";

        if (class_exists($class)) {
            $instance = new $class($this->client);

            $nestedResource = in_array(
                NestedResource::class, class_uses($instance)
            );

            return $nestedResource && $this instanceof Resource ?
                $instance->parent($this) : $instance;
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this|Resource
     */
    public function __call(string $name, array $arguments)
    {
        $resource = $this->$name;

        if ($resource instanceof Resource && is_callable($resource)) {
            $first = array_first($arguments);

            return is_array($first) ?
                $resource($first) :
                $resource($arguments);
        }
    }
}
