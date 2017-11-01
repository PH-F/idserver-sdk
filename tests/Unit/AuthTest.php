<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Class AuthTest
 *
 * @package Tests
 */
class AuthTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_login_a_user()
    {
        $client = app(\Xingo\IDServer\Client::class);
    }
}
