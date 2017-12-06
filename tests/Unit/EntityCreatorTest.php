<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\Stub;
use Tests\TestCase;
use Xingo\IDServer\Contracts\EloquentEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\EntityCreator;
use Xingo\IDServer\Resources;

class EntityCreatorTest extends TestCase
{
    /** @test */
    function it_can_replace_an_entity_instance_by_a_custom_one()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\User::class => Stub\FakeUser::class,
        ]);

        $creator = new EntityCreator(Resources\User::class);
        $entity = $creator->entity(['name' => 'John']);

        $this->assertInstanceOf(\Tests\Stub\FakeUser::class, $entity);
        $this->assertEquals('John', $entity->name);
    }

    /** @test */
    function it_cannot_return_a_custom_instance_that_does_not_extend_the_base_one()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\Subscription::class => Stub\FakeSubscription::class,
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Custom entity classes must extend the original one');

        $creator = new EntityCreator(Resources\Subscription::class);
        $creator->entity(['name' => 'John']);
    }

    /** @test */
    function it_works_if_the_base_class_is_an_eloquent_model()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\User::class => Stub\FakeEloquentModel::class,
        ]);

        $creator = new EntityCreator(Resources\User::class);
        $entity = $creator->entity(['name' => 'John', 'created_at' => '2017-02-03']);

        $this->assertInstanceOf(Stub\FakeEloquentModel::class, $entity);
        $this->assertEquals('John', $entity->name);
        $this->assertInstanceOf(Carbon::class, $entity->created_at);
        $this->assertInstanceOf(EloquentEntity::class, $entity);
    }
}
