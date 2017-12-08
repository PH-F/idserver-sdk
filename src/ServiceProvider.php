<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Xingo\IDServer\Client\Middleware\JwtToken;
use Xingo\IDServer\Client\Middleware\TokenExpired;
use Xingo\IDServer\Client\Support\JsonStream;

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

        $this->app->singleton('idserver.cli.client', function (Application $app) {
            return new Client($this->cliOptions($app));
        });

        $this->app->singleton('idserver.cli.manager', function (Application $app) {
            return new Manager($app->make('idserver.cli.client'));
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
        $handler = $this->getHandlerConfig();

        $handler->push(new JwtToken(), 'jwt-token');
        $handler->push(new TokenExpired(), 'jwt-token-expired');

        return [
            'base_uri' => $this->getClientBaseUri($app),
            'handler' => $handler,
            'headers' => [
                'X-XINGO-Client-ID' => $app['config']->get('idserver.store.client_id'),
                'X-XINGO-Secret-Key' => $app['config']->get('idserver.store.secret_key'),
            ],
        ];
    }

    /**
     * Get the CLI client options.
     *
     * @param Application $app
     * @return array
     */
    protected function cliOptions(Application $app): array
    {
        $handler = $this->getHandlerConfig();

        return [
            'base_uri' => $this->getClientBaseUri($app),
            'handler' => $handler,
            'headers' => [
                'X-XINGO-Client-ID' => $app['config']->get('idserver.store.cli.client_id'),
                'X-XINGO-Secret-Key' => $app['config']->get('idserver.store.cli.secret_key'),
            ],
        ];
    }

    /**
     * Get the handler config setup.
     *
     * @return HandlerStack
     */
    protected function getHandlerConfig(): HandlerStack
    {
        $handler = HandlerStack::create();

        $handler->push(Middleware::mapResponse(function (Response $response) {
            $stream = new JsonStream($response->getBody());

            return $response->withBody($stream);
        }));

        return $handler;
    }

    /**
     * Get the client base uri.
     *
     * @param Application $app
     * @return string
     */
    protected function getClientBaseUri(Application $app): string
    {
        return trim($app['config']->get('idserver.url'), '/') . '/';
    }
}
