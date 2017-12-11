<?php

namespace Xingo\IDServer\Concerns;

use Xingo\IDServer\Resources\Resource;

trait NestedResource
{
    /**
     * @var resource
     */
    public $parent;

    /**
     * @param resource $parent
     *
     * @return $this
     */
    public function parent(Resource $parent)
    {
        $this->parent = $parent;

        return $this;
    }
}
