<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Contracts\IdsEntity;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class Import extends Resource
{
    /**
     * @return IdsEntity
     */
    public function get(): IdsEntity
    {
        $this->call('GET', "imports/$this->id");

        return $this->makeEntity();
    }
}
