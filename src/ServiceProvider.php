<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Auth\SessionGuard;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Xingo\IDServer\Client\Middleware\JwtToken;
use Xingo\IDServer\Client\Middleware\TokenExpired;
use Xingo\IDServer\Client\Support\JsonStream;
use Xingo\IDServer\Events\TokenRefreshed;

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

        SessionGuard::macro('refreshRecaller', function () {
            if ($this->guest() || is_null($this->recaller())) {
                return;
            }

            $this->queueRecallerCookie($this->user());
        });

        Event::listen(TokenRefreshed::class, function () {
            if (auth()->guard() instanceof SessionGuard) {
                auth()->guard()->refreshRecaller();
            }
        });
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function options(Application $app): array
    {
        $handler = HandlerStack::create();

        $handler->push(new JwtToken(), 'jwt-token');
        $handler->push(new TokenExpired(), 'jwt-token-expired');

        $handler->push(Middleware::mapResponse(function (Response $response) {
            if (!$response->getBody()->isSeekable()) {
                return $response;
            }

            $stream = new JsonStream($response->getBody());

            return $response->withBody($stream);
        }));

        return [
            'base_uri' => trim(config('idserver.url'), '/') . '/',
            'handler' => $handler,
            'headers' => $this->getAuthenticationHeader(),
        ];
    }

    /**
     * @return array
     */
    private function getAuthenticationHeader(): array
    {
        $block = app()->runningInConsole() && !app()->runningUnitTests() ?
            'cli' : 'web';

        return [
            'X-XINGO-Client-ID' => config("idserver.store.$block.client_id"),
            'X-XINGO-Secret-Key' => config("idserver.store.$block.secret_key"),
        ];
    }
}
