<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

/**
 * Class Transaction
 *
 * @package Xingo\IDServer\Resources
 */
class Transaction extends Resource
{
    use ResourceBlueprint {
        get as protected getById;
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
        $this->asMultipart()->call('POST', 'transactions/import', $data);

        return $this->makeEntity(null, Entities\Transaction::class);
    }

}
