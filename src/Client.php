<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Str;

/**
 * Class Client
 *
 * @package Xingo\IDServer
 */
class Client
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->httpClient = $this->createHttpClient($config);
    }

    /**
     * @param array $config
     * @return HttpClient
     */
    private function createHttpClient(array $config): HttpClient
    {
        return new HttpClient($config);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $resource = Str::studly(Str::singular($name));
        $class = "Xingo\\IDServer\\Resources\\$resource";

        if (class_exists($class)) {
            return app()->make($class);
        }
    }
}
