<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Xingo\IDServer\Entities\Duration;
use Xingo\IDServer\Entities\Plan;

class DurationTest extends TestCase
{
    /** @test */
    public function it_is_priceable()
    {
        app()->setLocale('nl_NL');

        $item = new Duration([
            'price' => [
                'EUR' => 7000,
                'USD' => 9500,
            ],
        ]);

        $this->assertEquals('€ 70,00', $item->asPriceForHumans('price', 'EUR'));
    }
}
