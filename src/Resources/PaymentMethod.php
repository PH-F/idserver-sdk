<?php

namespace Xingo\IDServer\Resources;

class PaymentMethod extends Resource
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $this->call('GET', 'payment-methods', []);

        return $this->makeCollection();
    }
}
