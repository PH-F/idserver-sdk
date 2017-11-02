<?php

namespace Tests\Unit;

use Tests\TestCase;
use Xingo\IDServer\Client;
use Xingo\IDServer\Resources\User;

class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_the_correct_resource_using_magic_methods()
    {
        $client = app(Client::class);

        $this->assertInstanceOf(User::class, $client->users);
    }
}
