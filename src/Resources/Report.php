<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Contracts\IdsEntity;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class Report extends Resource
{
    /**
     * @param array $filters
     * @return IdsEntity
     */
    public function get(array $filters = []): IdsEntity
    {
        $this->call('GET', "reports/$this->id", $filters);

        return $this->makeEntity();
    }
}
