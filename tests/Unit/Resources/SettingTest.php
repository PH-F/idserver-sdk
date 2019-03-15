<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class SettingTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_gets_all_settings()
    {
        $this->mockResponse(200, [
            'data' => [
                ['entity_type' => 'store', 'entity_id' => 1],
                ['entity_type' => 'payment-method', 'entity_id' => 2],
            ],
        ]);

        $collection = $this->manager->navision->settings->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Navision\Setting::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals(2, $collection->last()->entity_id);

        $this->assertRequest(function(Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('navision/settings', $request->getUri()->getPath());
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_paginates_all_settings()
    {
        $this->mockResponse(200, [
            'data' => [
                ['entity_type' => 'payment-method', 'entity_id' => 2],
            ],
            'meta' => [
                'current_page' => 2,
                'per_page' => 1,
                'total' => 3
            ]
        ]);

        $collection = $this->manager->navision->settings
            ->paginate(2, 1)
            ->all();

        $this->assertCount(1, $collection);
        $this->assertEquals(2, $collection->first()->entity_id);
        $this->assertInstanceOf('stdClass', $collection->meta);
        $this->assertEquals(1, $collection->meta->per_page);
        $this->assertEquals(3, $collection->meta->total);

        $this->assertRequest(function(Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('navision/settings', $request->getUri()->getPath());
            $this->assertEquals('page=2&per_page=1', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->mockResponse(200, [
            'data' => [
                ['entity_type' => 'store', 'entity_id' => 1, 'attributes' => ['store_id' => 1]],
                ['entity_type' => 'payment-method', 'entity_id' => 1, 'attributes' => ['code' => 'mc']],
            ]
        ]);

        $settings = $this->manager->navision->settings->update($data = [
            ['entity_type' => 'store', 'entity_id' => 1, 'attribute' => 'store_id', 'value' => 1],
            ['entity_type' => 'payment-method', 'entity_id' => 1, 'attribute' => 'code', 'value' => 'mc'],
        ]);

        $this->assertInstanceOf(Entities\Navision\Setting::class, $settings->first());

        $this->assertRequest(function(Request $request) use($data) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('navision/settings', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($data), (string)$request->getBody());
        });
    }
}
