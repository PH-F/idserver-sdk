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
            return new Client(
                array_merge($this->baseOptions(), $this->jwtOptions())
            );
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
    protected function baseOptions(): array
    {
        return [
            'base_uri' => config('idserver.url'),
            'headers' => [
                'X-XINGO-Client-ID' => config('idserver.store.client_id'),
                'X-XINGO-Secret-Key' => config('idserver.store.secret_key'),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function jwtOptions(): array
    {
        return session()->has('jwt_token') ? [
            'headers' => [
                'Authorization' => sprintf(
                    'Bearer %s', session()->get('jwt_token')
                ),
            ],
        ] : [];
    }
}
