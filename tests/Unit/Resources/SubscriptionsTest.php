<?php

namespace Tests\Unit\Resources;

use Illuminate\Support\Collection;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Entities;

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
}
