<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\FilteredQuery;
use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class Variant
 *
 * @package Xingo\IDServer\Resources
 */
class Asset extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'plans-assets';
    }
}
