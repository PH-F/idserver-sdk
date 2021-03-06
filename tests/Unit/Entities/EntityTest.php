<?php

namespace Tests\Unit\Entities;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Entities;

class EntityTest extends TestCase
{
    use MockResponse;

    /** @test */
    public function it_creates_a_new_subscription_with_related_attributes()
    {
        $this->mockResponse(201, [
            'data' => [
                'id' => 1,
                'store' => ['id' => 2],
                'user' => ['id' => 3],
                'plan' => ['id' => 4],
                'original' => ['id' => 5],
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

        $this->assertInstanceOf(Entities\Subscription::class, $subscription->original);
        $this->assertEquals(5, $subscription->original->id);

        $this->assertInstanceOf(Entities\Order::class, $subscription->order);
        $this->assertEquals(6, $subscription->order->id);
    }

    /** @test */
    public function it_converts_string_date_fields_to_carbon_instances()
    {
        $this->mockResponse(200, ['data' => ['created_at' => '2017-12-31T23:05:13.000000Z']]);

        $user = $this->manager->users(1)->get();

        $this->assertInstanceOf(Carbon::class, $user->created_at);
        $this->assertEquals('31-12-2017', $user->created_at->format('d-m-Y'));
    }

    /** @test */
    public function it_converts_array_date_fields_to_carbon_instances()
    {
        $this->mockResponse(200, [
            'data' => [
                'created_at' => [
                    'date' => '2017-11-20 13:31:31.000000',
                    'timezone' => 'UTC',
                ],
            ],
        ]);

        $user = $this->manager->users(1)->get();

        $this->assertInstanceOf(Carbon::class, $user->created_at);
        $this->assertEquals('20-11-2017', $user->created_at->format('d-m-Y'));
    }

    /** @test */
    public function user_has_custom_date_fields()
    {
        $this->mockResponse(200, [
            'data' => ['date_of_birth' => '2017-12-31'],
        ]);

        $user = $this->manager->users(1)->get();

        $this->assertInstanceOf(Carbon::class, $user->date_of_birth);
    }

    /** @test */
    public function subscription_has_custom_date_fields()
    {
        $this->mockResponse(200, [
            'data' => [
                'start_date' => '2017-12-30',
                'end_date' => '2017-12-31',
            ],
        ]);

        $user = $this->manager->subscriptions(1)->get();

        $this->assertInstanceOf(Carbon::class, $user->start_date);
        $this->assertInstanceOf(Carbon::class, $user->end_date);
    }

    /** @test */
    public function it_can_be_converted_to_json()
    {
        $this->mockResponse(200, [
            'data' => [
                'name' => 'John Doe',
                'date_of_birth' => '2017-12-31',
                'store' => [
                    'name' => 'Foo Store',
                ],
            ],
        ]);

        $entity = $this->manager->subscriptions(1)->get();
        $json = $entity->toJson();

        $this->assertInternalType('string', $json);
        $this->isJson()->evaluate($json);
        $this->assertStringStartsWith('{', $json);
        $this->assertInternalType('array', json_decode($json, true));
    }

    /** @test */
    public function it_implements_array_access_interface()
    {
        $this->mockResponse(200, [
            'data' => [
                'foo' => 'Foo Bar',
                'created_at' => '2017-12-31',
                'store' => [
                    'name' => 'Foo Store',
                ],
            ],
        ]);

        $entity = $this->manager->subscriptions(1)->get();

        $this->assertEquals('Foo Bar', $entity['foo']);
        $this->assertInstanceOf(Carbon::class, $entity['created_at']);
        $this->assertInstanceOf(Entities\Store::class, $entity['store']);
        $this->assertEquals('Foo Store', $entity['store']->name);
    }
    
    /** @test */
    public function it_can_detect_attributes_with_isset()
    {
        $this->mockResponse(200, [
            'data' => [
                'foo' => 'Bar',
                'bar' => 'Foo',
            ],
        ]);

        $entity = $this->manager->users(1)->get();

        $this->assertTrue(isset($entity->foo));
        $this->assertTrue(isset($entity['foo']));
        $this->assertEquals('Bar', Arr::get($entity, 'foo'));
        $this->assertEquals('Bar', data_get($entity, 'foo'));
    }

    /** @test */
    public function it_can_load_methods_as_relations()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\User::class => \Tests\Stub\Entities\FakeUser::class,
        ]);

        $this->mockResponse(200, [
            'data' => [
                'foo' => 'Bar',
            ],
        ]);

        $user = $this->manager->users(1)->get();
        $this->assertInstanceOf(Collection::class, $user->abilities);
    }

    /** @test */
    public function it_will_store_the_result_of_a_relationship_call()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\User::class => \Tests\Stub\Entities\FakeUser::class,
        ]);

        $this->mockResponse(200, [
            'data' => [
                'foo' => 'Bar',
            ],
        ]);

        $user = $this->manager->users(1)->get();
        $this->assertEquals($user->abilities->first()->unique, $user->abilities->first()->unique);
    }
}
