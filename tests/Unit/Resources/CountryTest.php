<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class CountryTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_gets_all_countries()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->countries->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Country::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals(2, $collection->last()->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_paginates_all_countries()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 2],
            ],
            'meta' => [
                'current_page' => 2,
                'per_page' => 1,
                'total' => 3
            ]
        ]);

        $collection = $this->manager->countries
            ->paginate(2, 1)
            ->all();

        $this->assertCount(1, $collection);
        $this->assertEquals(2, $collection->first()->id);
        $this->assertInstanceOf('stdClass', $collection->meta);
        $this->assertEquals(1, $collection->meta->per_page);
        $this->assertEquals(3, $collection->meta->total);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('countries', $request->getUri()->getPath());
            $this->assertEquals('page=2&per_page=1', $request->getUri()->getQuery());
        });
    }


    /** @test */
    public function it_gets_just_one_country_by_id()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        $item = $this->manager->countries(1)->get();

        $this->assertInstanceOf(Entities\Country::class, $item);
        $this->assertInstanceOf(IdsEntity::class, $item);
        $this->assertEquals(1, $item->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('countries/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_can_be_created()
    {
        $params = [
            'code' => 'NL',
            'name' => [
                'nl' => 'Nederland',
                'en' => 'Netherlands'
            ]
        ];
        $this->mockResponse(201, [
            'data' => $params,
        ]);

        $country = $this->manager
            ->countries
            ->create(
                $params
            );

        $this->assertInstanceOf(Entities\Country::class, $country);
        $this->assertInstanceOf(IdsEntity::class, $country);
        $this->assertEquals($params['code'], $country->code);

        $this->assertRequest(function (Request $request) use ($params) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('countries', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($params), $request->getBody());
        });
    }

    public function it_can_be_updated()
    {
        $this->mockResponse(200);

        $code = str_random(3);
        $company = $this->manager->countries(3)->update([
            'code' => $code,
        ]);

        $this->assertInstanceOf(Entities\Country::class, $company);
        $this->assertInstanceOf(IdsEntity::class, $company);

        $this->assertRequest(function (Request $request) use ($code) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('countries/3', $request->getUri()->getPath());
            $this->assertEquals('code=' . $code, $request->getBody());
        });
    }
    
    public function it_can_be_deleted()
    {
        $this->mockResponse(204);
        $this->mockResponse(204);

        $result = $this->manager->countries(1)->delete();
        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('DELETE', $request->getMethod());
            $this->assertEquals('countries/1', $request->getUri()->getPath());
        });
    }
}
