<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Xingo\IDServer\Entities\FeaturedPlan;
use Xingo\IDServer\EntityCreator;

class FeaturedPlanTest extends TestCase
{
    /** @test */
    public function it_has_pros()
    {
        $plan = $this->getFeaturedPlan();

        $this->assertEquals([
            [
                'type' => 'pro',
                'name' => 'Voluptas non similique et porro mollitia et.',
                'position' => 1,
            ]
        ], $plan->pros());
    }

    /** @test */
    public function it_has_cons()
    {
        $plan = $this->getFeaturedPlan();

        $this->assertEquals([
            [
                'type' => 'con',
                'name' => 'A et eaque quis eaque dicta.',
                'position' => 3,
            ], [
                'type' => 'con',
                'name' => 'Aliquam exercitationem provident illo iure et voluptate ut et.',
                'position' => 4,
            ]
        ], $plan->cons());
    }

    /**
     * @return FeaturedPlan
     */
    protected function getFeaturedPlan(): FeaturedPlan
    {
        $data = [
            'details' => [
                0 => [
                    'type' => 'con',
                    'name' => 'Aliquam exercitationem provident illo iure et voluptate ut et.',
                    'position' => 4,
                ],
                1 => [
                    'type' => 'con',
                    'name' => 'A et eaque quis eaque dicta.',
                    'position' => 3,
                ],
                2 => [
                    'type' => 'pro',
                    'name' => 'Voluptas non similique et porro mollitia et.',
                    'position' => 1,
                ],
            ],
        ];

        return (new EntityCreator(null))
            ->entity($data, FeaturedPlan::class);
    }
}
