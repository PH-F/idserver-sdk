<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Xingo\IDServer\Concerns\CallableResources;
use Xingo\IDServer\Concerns\CustomExceptions;
use Xingo\IDServer\Contracts\EloquentEntity;
use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\EntityCreator;

abstract class Resource
{
    use CallableResources, CustomExceptions;

    /**
     * @var int | EloquentEntity
     */
    public $id;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var EntityCreator
     */
    protected $creator;

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
        $this->creator = new EntityCreator(static::class);
    }

    /**
     * @param int|Entity $param
     * @return Resource|$this
     */
    public function __invoke($param): Resource
    {
        if (!is_int($param)) {
            $param = $param->id ?? null;
        }

        $this->id = $param;

        return $this;
    }

    /**
     * @param Resource $class
     * @return string
     */
    public function toShortName(Resource $class = null): string
    {
        $class = $class ?: static::class;

        $shortName = (new \ReflectionClass($class))
            ->getShortName();

        return str_plural(strtolower($shortName));
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

        try {
            $response = $this->client->request($method, $uri, [
                $option => $params,
            ]);
        } catch (ClientException | ServerException $e) {
            $this->throwsException($e->getResponse());
        }

        $this->contents = $response->getBody()->asJson();

        return $response;
    }

    /**
     * @param array $attributes
     * @param string|null $class
     * @return Entity
     */
    protected function makeEntity(array $attributes = null, ?string $class = null): Entity
    {
        $attributes = $attributes ?: $this->contents['data'] ?? [];

        return $this->creator->entity($attributes, $class);
    }

    /**
     * @param array|null $data
     * @param array|null $meta
     * @return Collection
     */
    protected function makeCollection(array $data = null, array $meta = null): Collection
    {
        $data = $data ?: $this->contents['data'] ?? [];
        $meta = $meta ?: $this->contents['meta'] ?? [];

        return $this->creator->collection($data, $meta);
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
