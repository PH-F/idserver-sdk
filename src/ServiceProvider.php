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
        $this->app->singleton(Client::class, function (Application $app) {
            return new Client($this->options());
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
     * @return array
     */
    protected function options(): array
    {
        return [
            'base_uri' => config('idserver.url'),
            'headers' => [
                'X-XINGO-Client-ID' => config('idserver.store.client_id'),
                'X-XINGO-Secret-Key' => config('idserver.store.secret_key'),
            ],
        ];
    }
}
