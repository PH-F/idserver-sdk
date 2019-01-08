<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities\Order\Item;
use Xingo\IDServer\Entities\Order\Price;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class OrderItem extends Resource
{
    use ResourceBlueprint {
        all as protected;
        get as protected;
        create as protected;
        delete as protected;
    }

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'orders-items';
    }

    /**
     * Get the custom entity class to use
     *
     * @return string
     */
    protected function getEntityClass()
    {
        return Item::class;
    }
}
