<?php

namespace Tests\Unit\Entities\Traits;

use Tests\TestCase;
use Xingo\IDServer\Entities\Order\Item;
use Xingo\IDServer\Entities\Plan;

class PriceableTest extends TestCase
{
    /** @test */
    public function it_can_show_the_price_for_humans_based_on_locale()
    {
        $item = new Item();

        ids()->setLocale('nl_NL');
        $this->assertEquals('€ 75,00', $item->asPriceForHumans(7500, 'EUR'));
        $this->assertEquals('US$ 75,00', $item->asPriceForHumans(7500, 'USD'));

        ids()->setLocale('en_US');
        $this->assertEquals('€75.00', $item->asPriceForHumans(7500, 'EUR'));
        $this->assertEquals('$75.00', $item->asPriceForHumans(7500, 'USD'));
    }

    /** @test */
    public function it_can_show_the_price_for_humans_based_on_entity_field()
    {
        $item = new Item([
            'price' => 7500,
            'discount' => 1000,
            'shipping_cost' => 1000,
        ]);

        ids()->setLocale('nl_NL');
        $this->assertEquals('€ 75,00', $item->asPriceForHumans('price', 'EUR'));
        $this->assertEquals('US$ 75,00', $item->asPriceForHumans('price', 'USD'));

        ids()->setLocale('en_US');
        $this->assertEquals('€75.00', $item->asPriceForHumans('price', 'EUR'));
        $this->assertEquals('$75.00', $item->asPriceForHumans('price', 'USD'));
    }

    /** @test */
    public function it_can_show_the_price_for_an_array_field()
    {
        $item = new Plan([
            'price' => [
                'EUR' => 7000,
                'USD' => 9500,
            ],
        ]);

        ids()->setLocale('nl_NL');
        $this->assertEquals('€ 70,00', $item->asPriceForHumans('price', 'EUR'));
        $this->assertEquals('US$ 95,00', $item->asPriceForHumans('price', 'USD'));
    }

    /** @test */
    public function it_will_return_null_if_no_data_is_given()
    {
        $item = new Item([
            'price' => 7500,
        ]);

        ids()->setLocale('nl_NL');
        $this->assertNull($item->asPriceForHumans('invalid', 'EUR'));
        $this->assertNull($item->asPriceForHumans(null, 'EUR'));
    }
}
