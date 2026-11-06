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
    public function test_returns_the_correct_type_even_for_no_custom_classes()
    {
        $creator = new EntityCreator(Resources\User::class);
        $entity = $creator->entity(['name' => 'John']);

        $this->assertInstanceOf(IdsEntity::class, $entity);
        $this->assertEquals('John', $entity->name);
    }
    public function test_can_replace_an_entity_instance_by_a_custom_one()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\User::class => \Tests\Stub\Entities\FakeUser::class,
        ]);

        $creator = new EntityCreator(Resources\User::class);
        $entity = $creator->entity(['name' => 'John']);

        $this->assertInstanceOf(\Tests\Stub\Entities\FakeUser::class, $entity);
        $this->assertEquals('John', $entity->name);
    }
    public function test_cannot_return_a_custom_instance_that_does_not_extend_the_base_one()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\Subscription::class => \Tests\Stub\Standard\FakeSubscription::class,
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Custom entity classes must extend the original one');

        $creator = new EntityCreator(Resources\Subscription::class);
        $creator->entity(['name' => 'John']);
    }
    public function test_can_return_a_custom_instance_if_it_implements_the_right_interface()
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
    public function test_works_if_the_base_class_is_an_eloquent_model()
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
    public function test_adds_collection_relation_to_entity_class()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\Role::class => \Tests\Stub\Entities\FakeRole::class,
            Entities\Ability::class => \Tests\Stub\Entities\FakeAbility::class,
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
    public function test_adds_collection_relation_for_eloquent_model()
    {
        $this->app['config']->set('idserver.classes', [
            Entities\Role::class => \Tests\Stub\Eloquent\FakeRole::class,
            Entities\Ability::class => \Tests\Stub\Eloquent\FakeAbility::class,
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

        $this->assertInstanceOf(\Tests\Stub\Eloquent\FakeRole::class, $role);
        $this->assertEquals('admin', $role->name);
        $this->assertInstanceOf(Resources\Collection::class, $role->abilities);
        $this->assertInstanceOf(\Tests\Stub\Eloquent\FakeAbility::class, $role->abilities->first());
    }
    public function test_can_create_nested_relations()
    {
        $creator = new EntityCreator(Resources\Order::class);
        $order = $creator->entity([
            'items' => [
                [
                    'name' => 'delete_everything',
                    'plan_duration' => [
                        'name' => 'Duration x'
                    ],
                ], [
                    'name' => 'add_everything',
                    'plan_duration' => [
                        'name' => 'Duration y',
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(Entities\Duration::class, $order->items->first()->plan_duration);
        $this->assertEquals('Duration x', $order->items->first()->plan_duration->name);
    }

    public function test_hydrates_custom_entity_mappings(): void
    {
        foreach (self::customEntityMappings() as [$resource, $entity, $customEntity]) {
            $this->app['config']->set('idserver.classes', [$entity => $customEntity]);

            $instance = (new EntityCreator($resource))->entity(['name' => 'John']);

            self::assertInstanceOf($customEntity, $instance);
            self::assertSame('John', $instance->name);
        }
    }

    /**
     * @return array<string, array{0: class-string, 1: class-string, 2: class-string}>
     */
    public static function customEntityMappings(): array
    {
        return [
            'user' => [Resources\User::class, Entities\User::class, CustomUser::class],
            'address' => [Resources\Address::class, Entities\Address::class, CustomAddress::class],
            'subscription' => [Resources\Subscription::class, Entities\Subscription::class, CustomSubscription::class],
            'order' => [Resources\Order::class, Entities\Order::class, CustomOrder::class],
        ];
    }
}

class CustomUser extends Entities\User
{
}

class CustomAddress extends Entities\Address
{
}

class CustomSubscription extends Entities\Subscription
{
}

class CustomOrder extends Entities\Order
{
}
