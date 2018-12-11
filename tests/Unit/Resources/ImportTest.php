<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class ImportTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_gets_just_one_import_by_id()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        $import = $this->manager->imports(1)->get();

        $this->assertInstanceOf(Entities\Import::class, $import);
        $this->assertInstanceOf(IdsEntity::class, $import);
        $this->assertEquals(1, $import->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('imports/1', $request->getUri()->getPath());
        });
    }
}
