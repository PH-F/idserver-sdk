<?php

namespace Tests\Unit\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tests\TestCase;
use Xingo\IDServer\Exceptions\MissingJwtException;
use Xingo\IDServer\Manager;
use Xingo\IDServer\Middleware\Auth;

class AuthTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_there_is_no_jwt_in_the_session()
    {
        $this->expectException(MissingJwtException::class);

        $middleware = new Auth();
        $middleware->handle(new Request(), $this->closure());
    }

    /** @test */
    public function it_continues_the_request_if_a_jwt_is_present_in_the_session()
    {
        app(Manager::class)->setToken('token');

        $middleware = new Auth();
        $response = $middleware->handle(
            new Request(),
            $this->closure()
        );

        $this->assertNull($response);
    }

    /**
     * @return Closure
     */
    private function closure(): Closure
    {
        return function () {
        };
    }
}
