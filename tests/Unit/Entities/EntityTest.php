<?php

namespace Tests\Unit\Entities;

use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Entities;

class EntityTest extends TestCase
{
    use MockResponse;

    /** @test */
    function it_creates_a_new_subscription_with_related_attributes()
    {
        $this->mockResponse(201, [
            'data' => [
                'id' => 1,
                'store' => ['id' => 2],
                'user' => ['id' => 3],
                'plan' => ['id' => 4],
                'parent' => ['id' => 5],
                'order' => ['id' => 6],
            ],
        ]);

        $subscription = $this->manager->subscriptions->create([
            'store_id' => 1,
            'plan_id' => 1,
            'currency' => 'USD',
            'coupon' => 'NL2017',
        ]);

        $this->assertInstanceOf(Entities\Subscription::class, $subscription);
        $this->assertEquals(1, $subscription->id);

        $this->assertInstanceOf(Entities\Store::class, $subscription->store);
        $this->assertEquals(2, $subscription->store->id);

        $this->assertInstanceOf(Entities\User::class, $subscription->user);
        $this->assertEquals(3, $subscription->user->id);

        $this->assertInstanceOf(Entities\Plan::class, $subscription->plan);
        $this->assertEquals(4, $subscription->plan->id);

        $this->assertInstanceOf(Entities\Subscription::class, $subscription->parent);
        $this->assertEquals(5, $subscription->parent->id);

        $this->assertInstanceOf(Entities\Order::class, $subscription->order);
        $this->assertEquals(6, $subscription->order->id);
    }
}
