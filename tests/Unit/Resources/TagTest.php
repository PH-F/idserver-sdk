<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;

class TagTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_can_be_created_using_nested_resource()
    {
        $this->mockResponse(201, [
            'data' => [
                ['name' => 'foo'],
                ['name' => 'bar'],
            ],
        ]);

        $tags = $this->manager->users(1)
            ->tags->create(['foo', 'bar']);

        $this->assertEquals(['foo', 'bar'], $tags->pluck('name')->all());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/1/tags', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_can_list_all_tags_as_a_nested_resource()
    {
        $this->mockResponse(200, [
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Foo',
                ], [
                    'id' => 2,
                    'name' => 'Bar',
                ],
            ],
        ]);

        $tags = $this->manager->users(1)->tags()->all();

        $this->assertEquals(1, $tags->first()->id);
        $this->assertEquals(['Foo', 'Bar'], $tags->pluck('name')->all());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('users/1/tags', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_can_list_all_tags()
    {
        $this->mockResponse(201, [
            'data' => [
                ['name' => 'foo'],
                ['name' => 'bar'],
            ],
        ]);

        $tags = $this->manager->tags->all();

        $this->assertEquals(['foo', 'bar'], $tags->pluck('name')->all());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('tags', $request->getUri()->getPath());
        });
    }

    /** @test */
    public function it_can_filter_tags()
    {
        $this->mockResponse(201, [
            'data' => [
                ['name' => 'foo'],
            ],
        ]);

        $tags = $this->manager->tags->all([
            'name' => 'fo'
        ]);

        $this->assertEquals(['foo'], $tags->pluck('name')->all());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('GET', $request->getMethod());
            $this->assertEquals('tags', $request->getUri()->getPath());
            $this->assertEquals('name=fo&page=1&per_page=10', $request->getUri()->getQuery());
        });
    }
}
