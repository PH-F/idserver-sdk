<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Xingo\IDServer\Entities\ShippingCost;
use Xingo\IDServer\Entities\Plan;

class ShippingCostTest extends TestCase
{
    /** @test */
    public function it_is_priceable()
    {
        app()->setLocale('nl_NL');

        $item = new ShippingCost([
            'cost' => [
                'EUR' => 7000,
                'USD' => 9500,
            ],
        ]);

        $this->assertEquals('€ 70,00', $item->asPriceForHumans('cost', 'EUR'));
    }
}
