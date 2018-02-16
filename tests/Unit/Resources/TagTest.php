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
            'tags' => ['foo', 'bar'],
        ]);

        $tags = $this->manager->users(1)
            ->tags->create(['foo', 'bar']);

        $this->assertTrue(is_array($tags));
        $this->assertEquals(['foo', 'bar'], $tags);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/1/tags', $request->getUri()->getPath());
        });
    }
}
