<?php

namespace Tests\Unit\Entities\Order;

use Tests\TestCase;
use Xingo\IDServer\Entities\Order\Price;

class PriceTest extends TestCase
{
    /** @test */
    public function it_can_get_the_duration_discount()
    {
        app()->setLocale('nl_NL');

        $price = new Price([
            'currency' => 'EUR',
            'plan_variant_price' => 7000,
            'plan_duration_price' => 6000,
        ]);

        $this->assertEquals(1000, $price->duration_discount);
        $this->assertEquals('€ 10,00', $price->asPriceForHumans('duration_discount'));
    }
}
