<?php

namespace Xingo\IDServer;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function (Application $app) {
            return new Client([
                'base_uri' => $app['config']->get('idserver.url'),
                'headers' => [
                    'X-ELEKTOR-Signature' => config('idserver.store.signature'),
                ],
            ]);
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../onfig/idserver.php' => config_path('idserver.php'),
        ]);
    }
}
