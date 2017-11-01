<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Client;

/**
 * Class Resource
 *
 * @package Xingo\IDServer\Resources
 */
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
}
