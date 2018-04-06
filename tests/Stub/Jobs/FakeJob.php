<?php

namespace Tests\Stub\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use PHPUnit\Framework\Assert as PHPUnit;

class FakeJob implements ShouldQueue
{
    public function handle()
    {
        PHPUnit::assertEquals(
            'cli',
            ids()->client()->getConfig('headers')['X-XINGO-Client-ID']
        );

        PHPUnit::assertEquals(
            'cli',
            ids()->client()->getConfig('headers')['X-XINGO-Secret-Key']
        );
    }
}