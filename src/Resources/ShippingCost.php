<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class ShippingCost
 *
 * @package Xingo\IDServer\Resources
 */
class ShippingCost extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the custom resource name of this entity.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'shipping-costs';
    }
}
