<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class Duration
 *
 * @package Xingo\IDServer\Resources
 */
class Duration extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the custom resource name of this entity.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'plans-durations';
    }
}
