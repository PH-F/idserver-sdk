<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use Xingo\IDServer\Entities\Import;

class ImportTest extends TestCase
{
    /** @test */
    public function it_can_determine_if_its_finished()
    {
        $import = new Import;

        $import->status = Import::STATUS_FINISHED;
        $this->assertTrue($import->isFinished());

        $import->status = Import::STATUS_FAILED;
        $this->assertTrue($import->isFinished());

        $import->status = Import::STATUS_PENDING;
        $this->assertFalse($import->isFinished());

        $import->status = Import::STATUS_PROCESSING;
        $this->assertFalse($import->isFinished());
    }
}
