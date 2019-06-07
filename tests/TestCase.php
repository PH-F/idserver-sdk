<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Xingo\IDServer\ServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * Setup the test case and register test directory to be able to load stubs.
     */
    public function setUp()
    {
        parent::setUp();

        if (!defined('TEST_PATH')) {
            define('TEST_PATH', __DIR__ . '/');
        }
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }
}
