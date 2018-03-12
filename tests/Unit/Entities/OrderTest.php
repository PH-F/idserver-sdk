<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Xingo\IDServer\Entities\Order;
use Xingo\IDServer\EntityCreator;

class OrderTest extends TestCase
{
    /** @test */
    public function it_has_items()
    {
        $data = [
            'items' => [
                ['name' => 'Subscription X'],
                ['name' => 'Subscription Y'],
            ],
            'number' => 1234,
        ];

        $order = (new EntityCreator(null))
            ->entity($data, Order::class);

        $this->assertEquals('Subscription X', $order->items->first()->name);
    }
}
