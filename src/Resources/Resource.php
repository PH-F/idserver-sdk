<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use ReflectionClass;
use Xingo\IDServer\Concerns\CallableResources;
use Xingo\IDServer\Concerns\CustomExceptions;
use Xingo\IDServer\Entities\Entity;

abstract class Resource
{
    use CallableResources, CustomExceptions;

    /**
     * Entity id
     *
     * @var int
     */
    public $id;

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
        } catch (ClientException $e) {
            $this->checkValidation($e->getResponse());
            $this->checkAuthorization($e->getResponse());
            $this->checkForbidden($e->getResponse());
            $this->checkThrottle($e->getResponse());
        } catch (GuzzleServerException $e) {
            $this->checkServerError($e->getResponse());
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

        if ($class === null) {
            $entity = (new ReflectionClass(static::class))->getShortName();
            $class = sprintf('Xingo\\IDServer\\Entities\\%s', Str::studly($entity));
        }

        return new $class($attributes);
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

        $items = collect($data)->map(function ($item) {
            return $this->makeEntity($item);
        })->toArray();

        return new Collection($items, $meta);
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
