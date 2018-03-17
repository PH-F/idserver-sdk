<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources;

class CommunicationTest extends TestCase
{
    use Concerns\MockResponse;
    
    /** @test */
    public function it_gets_just_one_communication_by_id()
    {
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        $item = $this->manager->communications(1)->get();

        $this->assertInstanceOf(Entities\Communication::class, $item);
        $this->assertInstanceOf(IdsEntity::class, $item);
        $this->assertEquals(1, $item->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('communications/1', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_can_be_created_using_nested_resource()
    {
        $this->mockResponse(201, [
            'data' => ['street' => 'foo'],
        ]);

        $communication = $this->manager
            ->users(2)
            ->communications->create(
                $params = ['street' => 'foo']
            );

        $this->assertInstanceOf(Entities\Communication::class, $communication);
        $this->assertInstanceOf(IdsEntity::class, $communication);
        $this->assertEquals('foo', $communication->street);

        $this->assertRequest(function (Request $request) use ($params) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/2/communications', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($params), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_created_changing_the_base_resource_to_companies()
    {
        $this->mockResponse(201, [
            'data' => ['street' => 'foo'],
        ]);

        $communication = $this->manager
            ->companies(2)
            ->communications->create(
                $params = ['street' => 'foo']
            );

        $this->assertInstanceOf(Entities\Communication::class, $communication);
        $this->assertInstanceOf(IdsEntity::class, $communication);
        $this->assertEquals('foo', $communication->street);

        $this->assertRequest(function (Request $request) use ($params) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('companies/2/communications', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($params), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->mockResponse(200);

        $communication = $this->manager->communications(3)->update($data = [
            'value' => 'foo@bar.com',
        ]);

        $this->assertInstanceOf(Entities\Communication::class, $communication);
        $this->assertInstanceOf(IdsEntity::class, $communication);

        $this->assertRequest(function (Request $request) use($data) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('communications/3', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($data), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_updated_with_null_data()
    {
        $this->mockResponse(200);

        $this->manager->communications(3)->update([
            'foo' => null,
            'bar' => 0,
            'baz' => '',
        ]);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('communications/3', $request->getUri()->getPath());
            $this->assertEquals('foo=&bar=0&baz=', (string)$request->getBody());
        });
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->mockResponse(204);

        $result = $this->manager->communications(2)->delete();
        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('DELETE', $request->getMethod());
            $this->assertEquals('communications/2', $request->getUri()->getPath());
        });
    }
}
