<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\FilteredQuery;
use Xingo\IDServer\Concerns\NestedResource;
use Xingo\IDServer\Concerns\ResourceBlueprint;

/**
 * Class Download
 *
 * @package Xingo\IDServer\Resources
 */
class Download extends Resource
{
    use ResourceBlueprint;

    /**
     * Stream a zip with the invoices of the given orders.
     *
     * @param string|array $ordersIds CSV ("12,15,99") or array of order ids
     * @return \Closure
     */
    public function invoicesStream($ordersIds)
    {
        if (is_array($ordersIds)) {
            $ordersIds = implode(',', $ordersIds);
        }

        $ordersIds = preg_replace('/[^0-9,]/', '', (string) $ordersIds);

        $body = $this->stream('GET', $this->getResourceName() . '/invoices/' . $ordersIds);

        return function () use ($body) {
            while (!$body->eof()) {
                echo $body->read(8192);
                flush();
            }
        };
    }

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'downloads';
    }
}
