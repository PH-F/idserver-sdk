<?php

namespace Xingo\IDServer\Entities;

class Import extends Entity
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_FAILED = 'failed';
    const STATUS_FINISHED = 'finished';

    /**
     * @var array
     */
    protected $relationships = [
        'user' => User::class,
    ];

    public function isFinished(): bool
    {
        return in_array($this->status, [
            self::STATUS_FINISHED,
            self::STATUS_FAILED
        ]);
    }
}
