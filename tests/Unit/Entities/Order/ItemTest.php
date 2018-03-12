<?php

namespace Tests\Unit\Entities\Order;

use Tests\Concerns\MockResponse;
use Tests\TestCase;
use Xingo\IDServer\Entities\Order\Item;
use Xingo\IDServer\Entities\User;

class ItemTest extends TestCase
{
    use MockResponse;

    /** @test */
    public function it_can_get_the_total_price()
    {
        $item = new Item([
            'price' => 7000,
            'discount' => 1000,
            'shipping_cost' => 1000,
        ]);

        $this->assertEquals(5000, $item->total());
    }
}
