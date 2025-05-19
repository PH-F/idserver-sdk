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

        return $this->contents['data'];
    }


    public function checkApi($vatNumber)
    {
        $this->call('GET', "vies-api/$vatNumber");

        return $this->contents['data'];
    }

    /**
     * get all vat rules
     */
    public function rates($rateId = null)
    {
        $this->call('GET', "vat/rates/list/" . $rateId);
        return $this->contents['data'];
    }
}
