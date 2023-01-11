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
    public function import($data, $promotionId): IdsEntity
    {
        $this->asMultipart()->call('POST', 'coupons/import/' . $promotionId, $data);

        return $this->makeEntity(null, Entities\Coupon::class);
    }

    /**
     * get coupon and promotion by code via the idserver.
     *
     * @param $code
     *
     * @return IdsEntity
     */
    public function code(string $code)
    {
        $this->call('GET', "coupon/code/" . $code);

        return $this->makeEntity();
    }
}
