<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources;

class AddressesTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_gets_all_addresses()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->addresses->all();

        $this->assertInstanceOf(Resources\Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Address::class, $collection->first());
        $this->assertEquals(2, $collection->last()->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_paginates_all_addresses()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 2],
            ],
            'meta' => [
                'current_page' => 2,
                'per_page' => 1,
                'total' => 3
            ]
        ]);

        $collection = $this->manager->addresses->all(2, 1);

        $this->assertCount(1, $collection);
        $this->assertEquals(2, $collection->first()->id);
        $this->assertInstanceOf('stdClass', $collection->meta);
        $this->assertEquals(1, $collection->meta->per_page);
        $this->assertEquals(3, $collection->meta->total);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('addresses', $request->getUri()->getPath());
            $this->assertEquals('page=2&per_page=1', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_gets_just_one_address_by_id()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        $item = $this->manager->addresses(1)->get();

        $this->assertInstanceOf(Entities\Address::class, $item);
        $this->assertEquals(1, $item->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('addresses/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_can_be_created_using_nested_resource()
    {
        $this->mockResponse(201, [
            'data' => ['street' => 'foo'],
        ]);

        $address = $this->manager
            ->users(2)
            ->addresses->create(
                $params = ['street' => 'foo']
            );

        $this->assertInstanceOf(Entities\Address::class, $address);
        $this->assertEquals('foo', $address->street);

        $this->assertRequest(function (Request $request) use ($params) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/2/addresses', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($params), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_created_changing_the_base_resource_to_companies()
    {
        $this->mockResponse(201, [
            'data' => ['street' => 'foo'],
        ]);

        $address = $this->manager
            ->companies(2)
            ->addresses->create(
                $params = ['street' => 'foo']
            );

        $this->assertInstanceOf(Entities\Address::class, $address);
        $this->assertEquals('foo', $address->street);

        $this->assertRequest(function (Request $request) use ($params) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('companies/2/addresses', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($params), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->mockResponse(200);

        $company = $this->manager->addresses(3)->update([
            'street' => 'Somewhere Street',
        ]);

        $this->assertInstanceOf(Entities\Address::class, $company);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('addresses/3', $request->getUri()->getPath());
            $this->assertEquals('street=Somewhere+Street', $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->mockResponse(204);

        $result = $this->manager->addresses(2)->delete();
        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('DELETE', $request->getMethod());
            $this->assertEquals('addresses/2', $request->getUri()->getPath());
        });
    }
}
