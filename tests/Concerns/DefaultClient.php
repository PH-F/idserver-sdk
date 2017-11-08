<?php

namespace Tests\Concerns;

use Xingo\IDServer\Manager;

trait DefaultClient
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @before
     */
    protected function setupClient()
    {
        parent::setUp();

        $this->manager = app('idserver.manager');
    }
}
