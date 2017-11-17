<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Entities\Address;

class AddressesTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    function it_can_be_created_using_nested_resource()
    {
        $this->mockResponse(201, [
            'data' => ['street' => 'foo'],
        ]);

        $address = $this->manager
            ->users(2)
            ->addresses->create(
                $params = ['street' => 'foo']
            );

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('foo', $address->street);

        $this->assertRequest(function (Request $request) use ($params) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/2/addresses', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($params), $request->getBody());
        });
    }
}