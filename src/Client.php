<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client as HttpClient;

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

    public function __get($name)
    {

    }
}
