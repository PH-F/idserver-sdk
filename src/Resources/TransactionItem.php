<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities\Transaction\Item;

/**
 * Class TransactionItem
 *
 * @package Xingo\IDServer\Resources
 */
class TransactionItem extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'transactions-items';
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
