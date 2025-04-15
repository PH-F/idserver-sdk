<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities;

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

    /**
     * Get discounts for the user.
     *
     * @return Collection
     */
    public function discounts()
    {
        $query = $this->paginate(false)->queryString(['discount_group_id' => $this->id]);

        $this->call('GET', 'discounts', $query);

        return $this->makeCollection(null, null, Entities\Discount::class);
    }

    public function recipients()
    {
        $this->call('GET', $this->getResourceName . "/recipients/" . $this->id);
        return $this->makeCollection(null, null, Entities\DiscountGroup::class);
    }
}
