<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Contracts\IdsEntity;
use Xingo\IDServer\Entities;

/**
 * Class Coupon
 *
 * @package Xingo\IDServer\Resources
 */
class Coupon extends Resource
{
    use ResourceBlueprint;

    /**
     * Import bank transactions into the idserver.
     *
     * @param $data
     *
     * @return IdsEntity
     */
    public function import($data): IdsEntity
    {
        $this->asMultipart()->call('POST', 'coupons/import', $data);

        return $this->makeEntity(null, Entities\Coupon::class);
    }
}
