<?php

namespace Xingo\IDServer\Entities\Order;

use Xingo\IDServer\Entities\Duration;
use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Entities\Traits\Priceable;

class Price extends Entity
{
    use Priceable;

    /**
     * @var array
     */
    protected $relationships = [
        'plan_duration' => Duration::class,
    ];

    /**
     * Get the currency value of the entity.
     *
     * @return string
     */
    public function getCurrencyValue()
    {
        return $this->currency;
    }

    /**
     * Get the duration discount.
     *
     * @return int
     */
    public function getDurationDiscountAttribute()
    {
        return $this->plan_variant_price - $this->plan_duration_price;
    }
}
