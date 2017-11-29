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

    /** @test */
    function it_sends_correct_parameters_when_creating_a_new_subscription()
    {
        $this->mockResponse(201);

        $this->manager->subscriptions->create($attributes = [
            'store_id' => 1,
            'plan_id' => 1,
            'currency' => 'USD',
            'coupon' => 'NL2017',
        ]);

        $this->assertRequest(function (Request $request) use ($attributes) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('subscriptions', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($attributes), $request->getBody());
        });
    }

    /** @test */
    function it_do_not_send_a_missing_coupon_attribute_when_creating()
    {
        $this->mockResponse(201);

        $this->manager->subscriptions->create($attributes = [
            'store_id' => 1,
            'plan_id' => 1,
            'currency' => 'EUR',
        ]);

        $this->assertRequest(function (Request $request) use ($attributes) {
            $this->assertEquals(http_build_query($attributes), $request->getBody());
        });
    }

    /** @test */
    function it_can_be_renewed_using_a_plan_entity_instance_or_id()
    {
        $this->mockResponse(201);
        $this->mockResponse(201);

        $plan = new Entities\Plan(['id' => 2]);
        $subscription = $this->manager->subscriptions(1)->renew($plan);

        $this->assertInstanceOf(Entities\Subscription::class, $subscription);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('subscriptions/1/renew', $request->getUri()->getPath());
            $this->assertEquals('plan_id=2', $request->getBody());
        });

        $subscription = $this->manager->subscriptions(1)->renew(2);
        $this->assertInstanceOf(Entities\Subscription::class, $subscription);
    }
}
