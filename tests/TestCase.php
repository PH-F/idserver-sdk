<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Xingo\IDServer\ServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }
}
