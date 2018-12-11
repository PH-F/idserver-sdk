<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Entities\Import as ImportEntity;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class Import extends Resource
{
    /**
     * @return ImportEntity
     */
    public function get(): ImportEntity
    {
        $this->call('GET', "imports/$this->id");

        return $this->makeEntity();
    }
}
