<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use Xingo\IDServer\Concerns\CallableResource;
use Xingo\IDServer\Concerns\TokenSupport;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources;

/**
 * Class Client
 *
 * @property Resources\Address addresses
 * @method Resources\Address addresses(int | Entities\Address $resource)
 * @property Resources\Company companies
 * @method Resources\Company companies(int | Entities\Company $resource)
 * @property Resources\Role roles
 * @method Resources\Role roles(int | Entities\Role $resource)
 * @property Resources\Subscription subscriptions
 * @method Resources\Subscription subscriptions(int | Entities\Subscription $resource)
 * @property Resources\Store stores
 * @method Resources\Store stores(int | Entities\Store $resource)
 * @property Resources\User users
 * @method Resources\User users(int | array | Entities\User ...$resource)
 */
class Manager
{
    use CallableResource, TokenSupport;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    private $locale;

    /**
     * Manager constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->setClient($client);
    }

    /**
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Set the client to use.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Set the locale.
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
