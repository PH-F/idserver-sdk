<?php

namespace Xingo\IDServer\Entities;

class Promotion extends Entity
{
    /**
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
    ];
}
