<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

class VatProductGroup extends Resource
{
    use ResourceBlueprint {
        get as protected;
        create as protected;
        update as protected;
        delete as protected;
    }

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'vat/product-groups';
    }
}
