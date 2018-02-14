<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities\Ability;

/**
 * Class Role
 *
 * @package Xingo\IDServer\Resources
 * @property Ability abilities
 */
class Order extends Resource
{
    use ResourceBlueprint;
}
