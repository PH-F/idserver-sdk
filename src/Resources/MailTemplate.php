<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;
use Xingo\IDServer\Entities\Mail\Template;

class MailTemplate extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'mail-templates';
    }

    /**
     * Get the custom entity class.
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Template::class;
    }
}
