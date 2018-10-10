<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Auth\SessionGuard;
use Illuminate\Foundation\Application;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
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
        $this->app->singleton('idserver.client', function () {
            return new Client($this->options());
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

        $this->setupSessionHandling();

        $this->setupQueueHandling();
    }

    /**
     * @param string $type
     * @return array
     */
    protected function options($type = null): array
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
            'headers' => $this->getHeaders($type),
        ];
    }

    /**
     * @param null $type
     * @return array
     */
    private function getHeaders($type = null): array
    {
        return array_merge($this->getAuthenticationHeader($type), [
            'Accept-Language' => app()->getLocale(),
        ]);
    }

    /**
     * @param string $type
     * @return array
     */
    private function getAuthenticationHeader($type = null): array
    {
        $type = $this->getAuthenticationType($type);

        return [
            'X-XINGO-Client-ID' => config("idserver.store.$type.client_id"),
            'X-XINGO-Secret-Key' => config("idserver.store.$type.secret_key"),
        ];
    }

    /**
     * Setup the queue handling by using the CLI mode when running a job from the sync queue.
     */
    protected function setupQueueHandling()
    {
        Queue::before(function (JobProcessing $event) {
            if (!$event->job instanceof SyncJob) {
                return;
            }

            app('idserver.manager')->setClient(new Client($this->options('cli')));
        });

        Queue::after(function (JobProcessed $event) {
            if (!$event->job instanceof SyncJob) {
                return;
            }

            app('idserver.manager')->setClient(app('idserver.client'));
        });
    }

    protected function setupSessionHandling()
    {
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
     * @param string|null $type
     * @return string
     */
    private function getAuthenticationType($type = null): string
    {
        return $type ?: app()->runningInConsole() && !app()->runningUnitTests() ?
            'cli' : 'web';
    }
}
