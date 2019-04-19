<?php

namespace Xingo\IDServer\Concerns;

trait FilteredQuery
{
    use ResourceOrganizer;

    /**
     * @param array $filters
     * @return array
     */
    protected function queryString(array $filters): array
    {
        $organizer = $this->organizerQuery();

        return array_merge(['filter' => $filters], $organizer);
    }
}
