<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Xingo\IDServer\Entities\Plan;

class PlanTest extends TestCase
{
    /** @test */
    public function it_is_priceable()
    {
        ids()->setLocale('nl_NL');

        $item = new Plan([
            'price' => [
                'EUR' => 7000,
                'USD' => 9500,
            ],
        ]);

        $this->assertEquals('€ 70,00', $item->asPriceForHumans('price', 'EUR'));
    }
}
