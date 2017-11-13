<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use ReflectionClass;
use Xingo\IDServer\Concerns\CustomExceptions;
use Xingo\IDServer\Entities\Entity;

abstract class Resource
{
    use CustomExceptions;

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
     * @param int $id
     * @return Entity
     */
    abstract public function get(int $id);

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
        } catch (GuzzleServerException $e) {
            $this->checkServerError($e->getResponse());
        }

        $this->contents = $response->getBody()->asJson();

        return $response;
    }

    /**
     * @param array $attributes
     * @return Entity
     */
    protected function makeEntity(array $attributes = null): Entity
    {
        $attributes = $attributes ?: $this->contents['data'] ?? [];

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
