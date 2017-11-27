<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class SubscriptionsTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    function it_gets_all_subscriptions()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->subscriptions->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Subscription::class, $collection->first());
        $this->assertEquals(2, $collection->last()->id);
    }

    /** @test */
    function it_paginates_all_subscriptions()
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

        $collection = $this->manager->subscriptions->all(2, 1);

        $this->assertCount(1, $collection);
        $this->assertEquals(2, $collection->first()->id);
        $this->assertInternalType('array', $collection->meta);
        $this->assertEquals(1, $collection->meta['per_page']);
        $this->assertEquals(3, $collection->meta['total']);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('subscriptions', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'page' => 2,
                'per_page' => 1,
            ]), $request->getUri()->getQuery());
        });
    }
}
