<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class Plan
 *
 * @package Xingo\IDServer\Resources
 */
class Plan extends Resource
{
    use ResourceBlueprint;

    /**
     * Export the send list of the plan. This can be with or without filters.
     * It will return a callback printing the output of the stream.
     *
     * @param array $filters
     * @return \Closure
     */
    public function sendList(array $filters = [])
    {
        $body = $this->stream('GET', "plans/$this->id/send-list", [
            'filter' => $filters,
        ]);

        return function () use ($body) {
            while (!$body->eof()) {
                echo $body->read(1024);
            }
        };
    }
}
