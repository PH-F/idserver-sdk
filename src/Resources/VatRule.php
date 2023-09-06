<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

class VatRule extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'vat/rules';
    }

    public function check($vatNumber)
    {
        $this->call('GET', "vies/$vatNumber");

//        return (new Collection($this->contents['data']));
        return $this->contents['data'];
    }
}
