<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Exceptions\AuthorizationException;
use Xingo\IDServer\Exceptions\ForbiddenException;
use Xingo\IDServer\Exceptions\ValidationException;

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
     * @var Resource
     */
    protected $instance;

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
        if (is_int($param)) {
            $param = $this->get($param);
        }

        $this->instance = $param;

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
        }

        $this->contents = $response->getBody()->asJson();

        return $response;
    }

    /**
     * @param int $id
     * @return Entity
     */
    abstract public function get(int $id);

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

    /**
     * @param ResponseInterface $response
     * @throws ValidationException
     */
    private function checkValidation(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 422) {
            $content = $response->getBody()->asJson();

            throw new ValidationException($content['errors']);
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws AuthorizationException
     */
    private function checkAuthorization(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 401) {
            throw new AuthorizationException;
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws ForbiddenException
     */
    private function checkForbidden(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 403) {
            throw new ForbiddenException;
        }
    }
}
