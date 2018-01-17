<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;
use Xingo\IDServer\EntityCreator;
use Xingo\IDServer\Resources;

class EntityCreatorTest extends TestCase
{
    /** @test */
    public function it_returns_the_correct_type_even_for_no_custom_classes()
    {
        $creator = new EntityCreator(Resources\User::class);
        $entity = $creator->entity(['name' => 'John']);

        $this->assertInstanceOf(IdsEntity::class, $entity);
        $this->assertEquals('John', $entity->name);
    }

    /** @test */
    public function it_can_replace_an_entity_instance_by_a_custom_one()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\User::class => \Tests\Stub\Entities\FakeUser::class,
        ]);

        $creator = new EntityCreator(Resources\User::class);
        $entity = $creator->entity(['name' => 'John']);

        $this->assertInstanceOf(\Tests\Stub\Entities\FakeUser::class, $entity);
        $this->assertEquals('John', $entity->name);
    }

    /** @test */
    public function it_cannot_return_a_custom_instance_that_does_not_extend_the_base_one()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\Subscription::class => \Tests\Stub\Standard\FakeSubscription::class,
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Custom entity classes must extend the original one');

        $creator = new EntityCreator(Resources\Subscription::class);
        $creator->entity(['name' => 'John']);
    }

    /** @test */
    public function it_can_return_a_custom_instance_if_it_implements_the_right_interface()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\User::class => \Tests\Stub\Entities\FakeIdsEntity::class,
        ]);

        $creator = new EntityCreator(Resources\User::class);
        $entity = $creator->entity(['name' => 'John']);

        $this->assertInstanceOf(\Tests\Stub\Entities\FakeIdsEntity::class, $entity);
        $this->assertEquals('John', $entity->name);
        $this->assertInstanceOf(IdsEntity::class, $entity);
    }

    /** @test */
    public function it_works_if_the_base_class_is_an_eloquent_model()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\User::class => \Tests\Stub\Eloquent\FakeIdsModel::class,
        ]);

        $creator = new EntityCreator(Resources\User::class);
        $entity = $creator->entity(['name' => 'John', 'created_at' => '2017-02-03']);

        $this->assertInstanceOf(\Tests\Stub\Eloquent\FakeIdsModel::class, $entity);
        $this->assertEquals('John', $entity->name);
        $this->assertInstanceOf(Carbon::class, $entity->created_at);
        $this->assertInstanceOf(IdsEntity::class, $entity);
    }

    /** @test */
    public function it_adds_collection_relation_to_entity_class()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\Role::class => \Tests\Stub\Entities\FakeRole::class,
        ]);

        $creator = new EntityCreator(Resources\Role::class);
        $role = $creator->entity([
            'name' => 'admin',
            'title' => 'Administrator',
            'abilities' => [
                ['name' => 'delete_everything'],
                ['name' => 'add_everything'],
            ],
        ]);

        $this->assertInstanceOf(\Tests\Stub\Entities\FakeRole::class, $role);
        $this->assertEquals('admin', $role->name);
        $this->assertInstanceOf(Resources\Collection::class, $role->abilities);
        $this->assertInstanceOf(\Tests\Stub\Entities\FakeAbility::class, $role->abilities->first());
    }

    /** @test */
    public function it_adds_collection_relation_for_eloquent_model()
    {

    }
}
