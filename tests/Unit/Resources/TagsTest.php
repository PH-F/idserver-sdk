<?php

namespace Tests\Unit\Resources;

use Tests\Concerns;
use Tests\TestCase;

class TagsTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    function it_can_be_created_using_nested_resource()
    {
        $this->mockResponse(201, [
            'tags' => ['foo', 'bar'],
        ]);

        $tags = $this->manager->users(1)
            ->tags->create(['foo', 'bar']);

        $this->assertTrue(is_array($tags));
        $this->assertEquals(['foo', 'bar'], $tags);
    }
}