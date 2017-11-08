<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton('idserver.client', function (Application $app) {
            return new Client($this->options($app));
        });

        $this->app->singleton('idserver.manager', function (Application $app) {
            return new Manager($app->make('idserver.client'));
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/idserver.php' => config_path('idserver.php'),
        ]);
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function options(Application $app): array
    {
        return [
            'base_uri' => $app['config']->get('idserver.url'),
            'headers' => [
                'foo' => config('idserver.store.signature'),
            ],
        ];
    }
}
