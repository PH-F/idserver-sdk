<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_can_list_all_tags()
    {
        $this->mockResponse(201, [
            'data' => [
                ['name' => 'foo'],
                ['name' => 'bar'],
            ],
        ]);

        $methods = $this->manager->paymentMethods->all();

        $this->assertEquals(['foo', 'bar'], $methods->pluck('name')->all());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('payment-methods', $request->getUri()->getPath());
        });
    }
}
