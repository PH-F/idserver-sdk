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
     * @return Response
     */
    protected function call(string $method, string $uri, array $params = []): Response
    {
        $option = strtoupper($method) === 'GET' ?
            'query' : 'form_params';

        $response = $this->client->request($method, $uri, [
            $option => $params,
        ]);

        $json = $response->getBody()->getContents();
        $this->contents = json_decode($json, true);

        return $response;
    }

    /**
     * @param array $attributes
     * @return Entity
     */
    protected function makeEntity(array $attributes = null): Entity
    {
        $attributes = $attributes ?: $this->contents['data'];

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
