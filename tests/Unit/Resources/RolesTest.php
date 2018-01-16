<?php

namespace Tests\Unit\Resources;

use GuzzleHttp\Psr7\Request;
use Tests\Concerns;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources\Collection;

class RolesTest extends TestCase
{
    use Concerns\MockResponse;

    /** @test */
    public function it_gets_all_roles()
    {
        $this->mockResponse(200, [
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $collection = $this->manager->roles->all();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Entities\Role::class, $collection->first());
        $this->assertInstanceOf(IdsEntity::class, $collection->first());
        $this->assertEquals(2, $collection->last()->id);

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('roles', $request->getUri()->getPath());
            $this->assertEquals('page=1&per_page=10', $request->getUri()->getQuery());
        });
    }

    /** @test */
    public function it_can_update_abilities_when_updating_a_role()
    {
        $this->mockResponse(200, [
            'data' => [
                'id' => 1,
                'abilities' => $abilities = [
                    'drink_coffee',
                    'get_some_beer',
                ]
            ],
        ]);

        $role = $this->manager
            ->roles(1)
            ->update(
                ['name' => 'admin'],
                $abilities
            );

        $this->assertInstanceOf(Entities\Role::class, $role);
        $this->assertEquals(1, $role->id);
        $this->assertEquals($abilities, $role->abilities);
    }
}
