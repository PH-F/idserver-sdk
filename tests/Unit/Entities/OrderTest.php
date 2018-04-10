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

    /** @test */
    public function it_is_priceable()
    {
        app()->setLocale('nl_NL');

        $item = new Order([
            'currency' => [
                'abbreviation' => 'EUR',
            ],
            'total_amount' => 7000,
        ]);

        $this->assertEquals('€ 70,00', $item->asPriceForHumans('total_amount'));
    }
}
