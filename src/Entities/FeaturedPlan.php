<?php

namespace Xingo\IDServer\Entities;

class FeaturedPlan extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'plan_duration' => Duration::class,
    ];
}
