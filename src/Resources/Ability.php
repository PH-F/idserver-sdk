<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class Role
 *
 * @package Xingo\IDServer\Resources
 */
class Ability extends Resource
{
    use NestedResource;
    use ResourceBlueprint;
}
