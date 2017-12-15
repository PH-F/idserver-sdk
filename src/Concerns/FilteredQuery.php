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
        $pagination = $this->organizerQuery();
        $filters = array_only($filters, static::$filters ?? []);

        return array_merge($filters, $pagination);
    }
}
