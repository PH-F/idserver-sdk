<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class DiscountGroup
 *
 * @package Xingo\IDServer\Resources
 */
class DiscountGroup extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'discounts-groups';
    }
}
