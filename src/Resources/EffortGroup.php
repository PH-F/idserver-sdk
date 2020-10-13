<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities;

/**
 * Class EffortGroup
 *
 * @package Xingo\IDServer\Resources
 */
class EffortGroup extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'efforts-groups';
    }

    /**
     * Get efforts for the user.
     *
     * @return Collection
     */
    public function efforts()
    {
        $query = $this->paginate(false)->queryString(['effort_group_id' => $this->id]);

        $this->call('GET', 'efforts', $query);

        return $this->makeCollection(null, null, Entities\Effort::class);
    }
}
