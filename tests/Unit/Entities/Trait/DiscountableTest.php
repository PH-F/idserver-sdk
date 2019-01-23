<?php

namespace Tests\Unit\Entities\Traits;

use Tests\TestCase;
use Xingo\IDServer\Entities\VatRate;

class DiscountableTest extends TestCase
{
    /** @test */
    public function it_can_show_the_discount_for_humans_from_an_int()
    {
        $rate = new VatRate();
        $this->assertEquals('100%', $rate->asDiscountForHumans(10000));
        $this->assertEquals('50%', $rate->asDiscountForHumans(5000));
        $this->assertEquals('55.55%', $rate->asDiscountForHumans(5555));
    }

    /** @test */
    public function it_can_show_the_discount_for_humans_based_on_entity_field()
    {
        $rate = new VatRate([
            'amount' => 7500,
        ]);

        $this->assertEquals('75%', $rate->asDiscountForHumans('amount'));
    }

    /** @test */
    public function it_will_return_null_if_no_data_is_given()
    {
        $item = new VatRate([
            'amount' => 7500,
        ]);

        $this->assertNull($item->asDiscountForHumans('invalid'));
        $this->assertNull($item->asDiscountForHumans(null));
    }
}
