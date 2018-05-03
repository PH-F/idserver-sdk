<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

class NoteTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_can_be_created_using_nested_resource()
    {
        $this->mockResponse(201, [
            'data' => ['text' => 'foo'],
        ]);

        $note = $this->manager
            ->users(2)
            ->notes
            ->create(
                $params = ['text' => 'foo']
            );

        $this->assertInstanceOf(Entities\Note::class, $note);
        $this->assertInstanceOf(IdsEntity::class, $note);
        $this->assertEquals('foo', $note->text);

        $this->assertRequest(function (Request $request) use ($params) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals('users/2/notes', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($params), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_updated()
    {
        $this->mockResponse(200);

        $note = $this->manager->notes(3)->update($data = [
            'text' => 'foo bar',
        ]);

        $this->assertInstanceOf(Entities\Note::class, $note);
        $this->assertInstanceOf(IdsEntity::class, $note);

        $this->assertRequest(function (Request $request) use ($data) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('notes/3', $request->getUri()->getPath());
            $this->assertEquals(http_build_query($data), $request->getBody());
        });
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->mockResponse(204);

        $result = $this->manager->notes(2)->delete();
        $this->assertTrue($result);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('DELETE', $request->getMethod());
            $this->assertEquals('notes/2', $request->getUri()->getPath());
        });
    }
}
