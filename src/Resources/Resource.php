<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;
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

        $contents = $response->getBody()->getContents();

        return array_merge(
            json_decode($contents, true),
            ['status' => $response->getStatusCode()]
        );
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
}
