<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class GridTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_can_get_the_grid_data()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->grids('users')->data();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Grid::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals(2, $collection->last()->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('grids/users', $request->getUri()->getPath());
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_can_filter_the_grid()
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

        $collection = $this->manager->grids('users')
            ->paginate(2, 1)
            ->data([
                'name' => 'Foo',
            ]);

        $this->assertCount(1, $collection);
        $this->assertEquals(2, $collection->first()->id);
        $this->assertInstanceOf('stdClass', $collection->meta);
        $this->assertEquals(1, $collection->meta->per_page);
        $this->assertEquals(3, $collection->meta->total);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('grids/users', $request->getUri()->getPath());
            $this->assertEquals('name=Foo&page=2&per_page=1', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_can_export_the_grid_as_a_stream()
    {
        $this->mockResponse();

        $result = $this->manager->grids('users')->export();

        $this->assertInstanceOf(\Closure::class, $result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('grids/users/export', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_can_export_and_filter()
    {
        $this->mockResponse();

        $result = $this->manager->grids('users')->export([
            'name' => 'Foo',
        ]);

        $this->assertInstanceOf(\Closure::class, $result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('grids/users/export', $request->getUri()->getPath());
            $this->assertEquals('name=Foo', $request->getUri()->getQuery());
        });
    }
}
