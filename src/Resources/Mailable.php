<?php

namespace Xingo\IDServer\Resources;

class Mailable extends Resource
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $this->call('GET', 'mailables');

        return $this->makeCollection();
    }

    /**
     * Get the custom entity class.
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        return \Xingo\IDServer\Entities\Mail\Mailable::class;
    }
}
