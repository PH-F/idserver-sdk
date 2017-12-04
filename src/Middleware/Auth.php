<?php

namespace Xingo\IDServer\Middleware;

use Closure;
use Illuminate\Http\Request;
use Xingo\IDServer\Exceptions\MissingJwtException;
use Xingo\IDServer\Manager;

class Auth
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     * @throws MissingJwtException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        /** @var Manager $manager */
        $manager = app()->make(Manager::class);

        if (!$manager->hasToken()) {
            throw new MissingJwtException();
        }

        return $next($request);
    }
}
