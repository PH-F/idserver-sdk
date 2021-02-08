<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

/**
 * Class Dunning
 *
 * @package Xingo\IDServer\Resources
 */
class Dunning extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the custom resource name of this entity.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'dunnings';
    }

    /**
     * Import bank transactions into the idserver.
     *
     * @param $data
     *
     * @return IdsEntity
     */
    public function import($data): IdsEntity
    {
        $this->asMultipart()->call('POST', 'dunnings/import', $data);

        return $this->makeEntity(null, Entities\Dunning::class);
    }

    public function reminder()
    {
        $this->call('PUT', 'dunnings/reminder');

        return $this->makeCollection();
    }
}
