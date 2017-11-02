<?php

namespace Tests\Concerns;

use Xingo\IDServer\Client;

trait DefaultClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @before
     */
    protected function setupClient()
    {
        parent::setUp();
        $this->client = app(Client::class);
    }
}
