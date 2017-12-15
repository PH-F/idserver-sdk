<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class StoresTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_gets_all_stores()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->stores->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Store::class, $collection->first());
        $this->assertEquals(2, $collection->last()->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_paginates_all_stores()
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

        $collection = $this->manager->stores
            ->paginate(2, 1)
            ->all();

        $this->assertCount(1, $collection);
        $this->assertEquals(2, $collection->first()->id);
        $this->assertInstanceOf('stdClass', $collection->meta);
        $this->assertEquals(1, $collection->meta->per_page);
        $this->assertEquals(3, $collection->meta->total);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('stores', $request->getUri()->getPath());
            $this->assertEquals('page=2&per_page=1', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_gets_just_one_store_by_id()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        $item = $this->manager->stores(1)->get();

        $this->assertInstanceOf(Entities\Store::class, $item);
        $this->assertEquals(1, $item->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('stores/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_sends_correct_parameters_when_creating_a_new_store()
    {
        $this->mockResponse(201);

        $this->manager->stores->create($attributes = [
            'name' => 'Acme Store',
            'url' => 'http://google.com',
            'currencies' => ['USD', 'EUR'],
        ]);

        $this->assertRequest(function (Request $request) use ($attributes) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('stores', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($attributes), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->mockResponse(200);

        $company = $this->manager->stores(3)->update([
            'name' => 'Acme Store',
        ]);

        $this->assertInstanceOf(Entities\Store::class, $company);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('stores/3', $request->getUri()->getPath());
            $this->assertEquals('name=Acme+Store', $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->mockResponse(204);

        $result = $this->manager->stores(2)->delete();
        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('DELETE', $request->getMethod());
            $this->assertEquals('stores/2', $request->getUri()->getPath());
        });
    }
}
