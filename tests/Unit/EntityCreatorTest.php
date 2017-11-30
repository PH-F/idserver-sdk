<?php

namespace Tests\Unit;

use Tests\TestCase;
use Xingo\IDServer\EntityCreator;
use Xingo\IDServer\Resources\User;

class EntityCreatorTest extends TestCase
{
    /** @test */
    function it_can_replace_an_entity_instance_by_a_custom_one()
    {
        $this->app['config']->set('idserver.classes', [
            \Xingo\IDServer\Entities\User::class => \Tests\Stub\FakeUser::class,
        ]);

        $creator = new EntityCreator(User::class);
        $entity = $creator->entity(['name' => 'John']);

        $this->assertInstanceOf(\Tests\Stub\FakeUser::class, $entity);
        $this->assertEquals('John', $entity->name);
    }
}
