<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Entities\Report;
use Xingo\IDServer\Resources;

class ReportTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_can_get_a_certain_report()
    {
        $this->mockResponse(200, [
            'data' => [
                ['count' => 7],
                ['count' => 8],
            ],
        ]);

        $collection = $this->manager->reports('funnel-subscriptions')->get([
            'date' => '2018-01-01',
        ]);

        $this->assertInstanceOf(Resources\Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Report::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('reports/funnel-subscriptions', $request->getUri()->getPath());
            $this->assertEquals('filter%5Bdate%5D=2018-01-01', $request->getUri()->getQuery());
        });
    }
}
