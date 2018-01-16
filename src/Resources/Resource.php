<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Xingo\IDServer\Concerns\CallableResources;
use Xingo\IDServer\Concerns\CustomExceptions;
use Xingo\IDServer\Concerns\ResourceOrganizer;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\EntityCreator;

abstract class Resource
{
    use CallableResources;
    use CustomExceptions;
    use ResourceOrganizer;

    /**
     * @var int | IdsEntity | array
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
     * @param array $param
     * @return Resource|$this
     */
    public function __invoke(array $param): Resource
    {
        $ids = collect($param)->map(function ($param) {
            return is_object($param) ? $param->id : $param;
        });

        $this->id = $ids->count() > 1 ?
            $ids->all() :
            $ids->first();

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
     * @throws \Xingo\IDServer\Exceptions\AuthorizationException
     * @throws \Xingo\IDServer\Exceptions\ForbiddenException
     * @throws \Xingo\IDServer\Exceptions\NotFoundException
     * @throws \Xingo\IDServer\Exceptions\ServerException
     * @throws \Xingo\IDServer\Exceptions\ThrottleException
     * @throws \Xingo\IDServer\Exceptions\ValidationException
     */
    protected function call(string $method, string $uri, array $params = []): Response
    {
        $option = strtoupper($method) === 'GET' ?
            'query' : 'form_params';

        try {
            $response = $this->client->request($method, $uri, [
                $option => $this->convertNullToEmptyString($params),
            ]);
        } catch (ClientException | ServerException $e) {
            $this->throwsException($e->getResponse());
        }

        $this->contents = $response->getBody()->asJson();

        return $response;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function convertNullToEmptyString(array $params): array
    {
        return collect($params)->mapWithKeys(function ($value, $field) {
            $value = null === $value ? '' : $value;

            return [$field => $value];
        })->all();
    }

    /**
     * @param array $attributes
     * @param string|null $class
     * @return IdsEntity
     */
    protected function makeEntity(array $attributes = null, ?string $class = null): IdsEntity
    {
        $attributes = $attributes ?: $this->contents['data'] ?? [];

        return $this->creator->entity($attributes, $class);
    }

    /**
     * @param array|null $data
     * @param array|null $meta
     * @param null|string $class
     * @return Collection
     */
    protected function makeCollection(
        array $data = null,
        array $meta = null,
        ?string $class = null
    ): Collection {
        $data = $data ?: $this->contents['data'] ?? [];
        $meta = $meta ?: $this->contents['meta'] ?? [];

        return $this->creator->collection($data, $meta, $class);
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
