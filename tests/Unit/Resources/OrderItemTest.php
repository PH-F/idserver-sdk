<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Entities\Order\Price;
use Xingo\IDServer\Resources\Collection;

class OrderItemTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_can_be_updated()
    {
        $this->mockResponse(200);

        $item = $this->manager->orderItems(3)->update([
            'base_price' => 300,
            'discount' => 300,
            'shipping_cost' => 300,
        ]);

        $this->assertInstanceOf(Entities\Order\Item::class, $item);
        $this->assertInstanceOf(IdsEntity::class, $item);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('orders-items/3', $request->getUri()->getPath());
            $this->assertEquals('base_price=300&discount=300&shipping_cost=300', $request->getBody());
        });
    }
}
