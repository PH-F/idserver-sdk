<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class MailableTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_gets_all_mailables()
    {
        $this->mockResponse(200, [
            'data' => [
                ['key' => 'App\\Foo'],
                ['key' => 'App\\Bar'],
            ],
        ]);

        $collection = $this->manager->mailables->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Mail\Mailable::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals('App\\Bar', $collection->last()->key);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('mailables', $request->getUri()->getPath());
            $this->assertEmpty($request->getUri()->getQuery());
        });
    }
}
