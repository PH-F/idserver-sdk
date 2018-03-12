<?php

namespace Xingo\IDServer\Entities\Plan;

use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Entities\Plan;

class Variant extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'plan' => Plan::class,
    ];
}
