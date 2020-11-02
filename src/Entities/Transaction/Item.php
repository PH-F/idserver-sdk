<?php

namespace Xingo\IDServer\Entities\Transaction;

use Xingo\IDServer\Entities\Entity;
use Xingo\IDServer\Entities\Order\Invoice;

class Item extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'invoice' => Invoice::class,
    ];
}
