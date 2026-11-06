<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Xingo\IDServer\Entities\Plan;

class PlanTest extends TestCase
{
    public function test_is_priceable()
    {
        app()->setLocale('nl_NL');

        $item = new Plan([
            'price' => [
                'EUR' => 7000,
                'USD' => 9500,
            ],
        ]);

        $this->assertEquals('€ 70,00', $item->asPriceForHumans('price', 'EUR'));
    }
}
