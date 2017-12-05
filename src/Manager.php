<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use Xingo\IDServer\Concerns\CallableResources;
use Xingo\IDServer\Concerns\TokenSupport;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources;

/**
 * Class Client
 *
 * @property Resources\Company companies
 * @method Resources\Company companies(int | Entities\Company $resource)
 * @property Resources\Subscription subscriptions
 * @method Resources\Subscription subscriptions(int | Entities\Subscription $resource)
 * @property Resources\User users
 * @method Resources\User users(int | array | Entities\User ...$resource)
 */
class Manager
{
    use CallableResources, TokenSupport;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Manager constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }
}
