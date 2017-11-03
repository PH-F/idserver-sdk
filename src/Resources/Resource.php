<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use ReflectionClass;
use Xingo\IDServer\Entities\Entity;

abstract class Resource
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $contents;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return array
     */
    protected function call(string $method, string $uri, array $params = []): array
    {
        $option = strtoupper($method) === 'GET' ?
            'query' : 'form_params';

        $response = $this->client->request($method, $uri, [
            $option => $params,
        ]);

        $json = $response->getBody()->getContents();

        $this->contents = array_merge(
            json_decode($json, true),
            ['status' => $response->getStatusCode()]
        );

        return $this->contents;
    }

    /**
     * @return Entity
     */
    protected function entity()
    {
        if ($this->success()) {
            return $this->makeEntity($this->contents['data']);
        }
    }

    /**
     * @param array $attributes
     * @return Entity
     */
    protected function makeEntity(array $attributes): Entity
    {
        $entity = (new ReflectionClass(static::class))->getShortName();
        $class = sprintf('Xingo\\IDServer\\Entities\\%s', Str::studly($entity));

        return new $class($attributes);
    }

    /**
     * @return bool
     */
    protected function success()
    {
        return $this->contents['status'] === 200 ||
            $this->contents['status'] === 201;
    }
}
