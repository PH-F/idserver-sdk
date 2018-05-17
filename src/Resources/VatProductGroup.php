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
}
