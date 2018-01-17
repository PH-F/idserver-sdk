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
        // Call to update /roles/1
        $this->mockResponse(200, ['data' => ['id' => 1]]);

        // Call to update /roles/1/abilities
        $this->mockResponse(200, [
            'data' => [
                [
                    'name' => 'drink_coffee',
                    'title' => 'Drink Coffee',
                ],
                [
                    'name' => 'get_some_beer',
                    'title' => 'Get some beer',
                ],
            ],
        ]);

        $role = $this->manager
            ->roles(1)
            ->update([], ['drink_coffee', 'get_some_beer']);

        $this->assertInstanceOf(Entities\Role::class, $role);
        $this->assertEquals(1, $role->id);
        $this->assertInstanceOf(Collection::class, $role->abilities);
        $this->assertInstanceOf(Entities\Ability::class, $role->abilities->first());

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('roles/1', $request->getUri()->getPath());
        });

        $this->assertRequest(function (Request $request) {
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals('roles/1/abilities', $request->getUri()->getPath());
            $this->assertEquals(http_build_query([
                'drink_coffee',
                'get_some_beer',
            ]), (string)$request->getBody());
        });
    }

    /** @test */
    public function it_always_have_a_abilities_collection_as_property()
    {
        $this->mockResponse(200, [
            'data' => [
                'abilities' => [
                    [
                        'name' => 'foo',
                        'title' => 'Foo',
                    ],
                ],
            ],
        ]);

        $role = $this->manager->roles(1)->get();

        $this->assertInstanceOf(Collection::class, $role->abilities);
        $this->assertInstanceOf(Entities\Ability::class, $role->abilities->first());
    }
}
