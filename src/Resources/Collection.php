<?php

namespace Xingo\IDServer\Resources;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * @var array
     */
    public $meta = [];

    /**
     * @param array $items
     * @param array $meta
     */
    public function __construct($items = [], array $meta)
    {
        parent::__construct($items);
        $this->meta = $meta;
    }
}
