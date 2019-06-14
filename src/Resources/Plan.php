<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities;

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
     * @param  array  $filters
     *
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

    /**
     * Get users that have the plan. By default it will send the users that currently have
     * an active subscription. It's also possible to send a date for which you want to
     * have the results.
     *
     * @param  array  $filters
     *
     * @return Collection
     */
    public function users(array $filters = [])
    {
        $query = $this->queryString($filters);

        $this->call('GET', "plans/$this->id/users", $query);

        return $this->makeCollection(null, null, Entities\Subscription::class);
    }
}
