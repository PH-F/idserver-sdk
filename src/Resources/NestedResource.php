<?php

namespace Xingo\IDServer\Resources;

class NestedResource extends Resource
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
