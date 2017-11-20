<?php

namespace Xingo\IDServer\Concerns;

use Xingo\IDServer\Resources\Resource;

trait NestedResource
{
    /**
     * @var Resource
     */
    public $parent;

    /**
     * @param Resource $parent
     * @return $this
     */
    public function parent(Resource $parent)
    {
        $this->parent = $parent;

        return $this;
    }
}
