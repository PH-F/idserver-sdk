<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Xingo\IDServer\Concerns\CallableResource;
use Xingo\IDServer\Concerns\CustomException;
use Xingo\IDServer\Concerns\ResourceOrganizer;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\EntityCreator;

abstract class Resource
{
    use CallableResource;
    use CustomException;
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
        $response = $this->request($method, $uri, $params);

        $this->contents = $response->getBody()->asJson();

        return $response;
    }

    /**
     * Make a call and stream the response.
     *
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     * @throws \Xingo\IDServer\Exceptions\AuthorizationException
     * @throws \Xingo\IDServer\Exceptions\ForbiddenException
     * @throws \Xingo\IDServer\Exceptions\NotFoundException
     * @throws \Xingo\IDServer\Exceptions\ServerException
     * @throws \Xingo\IDServer\Exceptions\ThrottleException
     * @throws \Xingo\IDServer\Exceptions\ValidationException
     */
    protected function stream(string $method, string $uri, array $params = [])
    {
        $response = $this->request($method, $uri, $params, [
            'stream' => true,
        ]);

        return $response->getBody();
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
    ): Collection
    {
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

    /**
     * Make the Guzzle request.
     *
     * @param string $method
     * @param string $uri
     * @param array $params
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Xingo\IDServer\Exceptions\AuthorizationException
     * @throws \Xingo\IDServer\Exceptions\ForbiddenException
     * @throws \Xingo\IDServer\Exceptions\NotFoundException
     * @throws \Xingo\IDServer\Exceptions\ServerException
     * @throws \Xingo\IDServer\Exceptions\ThrottleException
     * @throws \Xingo\IDServer\Exceptions\ValidationException
     */
    private function request(string $method, string $uri, array $params, array $options = [])
    {
        $parameter = strtoupper($method) === 'GET' ? 'query' : 'form_params';

        $options = array_merge([
            $parameter => $this->convertNullToEmptyString($params),
        ], $options);

        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (ClientException | ServerException $e) {
            $this->throwsException($e->getResponse());
        }

        return $response ?? null;
    }
}
