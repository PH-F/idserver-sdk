<?php

namespace Tests\Unit;

use Tests\Stub\Jobs\FakeJob;
use Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_sets_the_cli_mode_for_jobs_in_sync()
    {
        $this->assertEquals('web', ids()->client()->getConfig('headers')['X-XINGO-Client-ID']);
        $this->assertEquals('web', ids()->client()->getConfig('headers')['X-XINGO-Secret-Key']);

        dispatch(new FakeJob());

        $this->assertEquals('web', ids()->client()->getConfig('headers')['X-XINGO-Client-ID']);
        $this->assertEquals('web', ids()->client()->getConfig('headers')['X-XINGO-Secret-Key']);
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('idserver.store.web.client_id', 'web');
        $app['config']->set('idserver.store.web.secret_key', 'web');
        $app['config']->set('idserver.store.cli.client_id', 'cli');
        $app['config']->set('idserver.store.cli.secret_key', 'cli');
    }
}
