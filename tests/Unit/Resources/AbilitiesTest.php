<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources;

class AbilitiesTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_lists_all_abilities()
    {
        $this->mockResponse(200, [
            'data' => [
                ['name' => '*'],
                ['name' => 'users.index'],
            ],
        ]);

        $collection = $this->manager->abilities->all();

        $this->assertInstanceOf(Resources\Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Ability::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals('users.index', $collection->last()->name);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('abilities', $request->getUri()->getPath());
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }
}
