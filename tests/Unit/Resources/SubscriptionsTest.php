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

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
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
        $this->assertInstanceOf('stdClass', $collection->meta);
        $this->assertEquals(1, $collection->meta->per_page);
        $this->assertEquals(3, $collection->meta->total);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('subscriptions', $request->getUri()->getPath());
            $this->assertEquals('page=2&per_page=1', $request->getUri()->getQuery());
        });
    }

    /** @test */
    function it_gets_just_one_subscription_by_id()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        $item = $this->manager->subscriptions(1)->get();

        $this->assertInstanceOf(Entities\Subscription::class, $item);
        $this->assertEquals(1, $item->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('subscriptions/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    function it_gets_expiring_subscriptions()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $items = $this->manager->subscriptions->expiring(10);

        $this->assertInstanceOf(Collection::class, $items);
        $this->assertCount(2, $items);
        $this->assertInstanceOf(Entities\Subscription::class, $items->first());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('subscriptions/expiring', $request->getUri()->getPath());
            $this->assertEquals('days=10', $request->getUri()->getQuery());
        });
    }
}
