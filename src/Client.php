<?php

namespace Xingo\IDServer;

use Illuminate\Support\Str;

class Client
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
            return app()->make($class, [
                app()->make(\GuzzleHttp\Client::class)
            ]);
        }
    }
}
