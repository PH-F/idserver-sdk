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

    /** @test */
    public function it_can_set_and_return_the_jwt_token()
    {
        $client = app(Client::class);
        $client->setToken('my-token');

        $this->assertEquals('my-token', $client->getToken());
    }


    /** @test */
    public function it_will_return_an_empty_string_when_no_token_is_set_in_the_session()
    {
        $client = app(Client::class);

        $this->assertEquals('', $client->getToken());
    }
}
