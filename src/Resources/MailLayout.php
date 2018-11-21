<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities\Mail\Layout;

class MailLayout extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'mail-layouts';
    }

    /**
     * Get the custom entity class.
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Layout::class;
    }
}
