<?php

namespace Xingo\IDServer\Resources;

class VatProductGroup extends Resource
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $this->call('GET', 'vat/product-groups');

        return $this->makeCollection();
    }
}
