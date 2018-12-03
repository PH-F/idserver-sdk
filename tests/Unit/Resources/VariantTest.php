<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class VariantTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_gets_all_variants()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->variants->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Variant::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals(2, $collection->last()->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_paginates_all_variants()
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

        $collection = $this->manager->variants
            ->paginate(2, 1)
            ->all();

        $this->assertCount(1, $collection);
        $this->assertEquals(2, $collection->first()->id);
        $this->assertInstanceOf('stdClass', $collection->meta);
        $this->assertEquals(1, $collection->meta->per_page);
        $this->assertEquals(3, $collection->meta->total);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('plans-variants', $request->getUri()->getPath());
            $this->assertEquals('page=2&per_page=1', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_gets_just_one_variant_by_id()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        $item = $this->manager->variants(1)->get();

        $this->assertInstanceOf(Entities\Variant::class, $item);
        $this->assertInstanceOf(IdsEntity::class, $item);
        $this->assertEquals(1, $item->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('plans-variants/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_sends_correct_parameters_when_creating_a_new_variant()
    {
        $this->mockResponse(201);

        $this->manager->variants->create($attributes = [
            'name' => 'Acme Promotion',
        ]);

        $this->assertRequest(function (Request $request) use ($attributes) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('plans-variants', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($attributes), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->mockResponse(200);

        $company = $this->manager->variants(3)->update([
            'name' => 'Acme Promotion',
        ]);

        $this->assertInstanceOf(Entities\Variant::class, $company);
        $this->assertInstanceOf(IdsEntity::class, $company);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('plans-variants/3', $request->getUri()->getPath());
            $this->assertEquals('name=Acme+Promotion', $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->mockResponse(204);

        $result = $this->manager->variants(2)->delete();
        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('DELETE', $request->getMethod());
            $this->assertEquals('plans-variants/2', $request->getUri()->getPath());
        });
    }
}
