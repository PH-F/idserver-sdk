<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\FilteredQuery;
use Xingo\IDServer\Entities\Navision\Setting as SettingEntity;

class Setting extends Resource
{
    use FilteredQuery;

    /**
     * @param array $filters
     *
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', 'navision/settings', $query);

        return $this->makeCollection();
    }

    public function update(array $attributes): Collection
    {
        $this->call('PUT', 'navision/settings', $attributes);

        return $this->makeCollection();
    }

    /**
     * Get the custom entity class.
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        return SettingEntity::class;
    }
}
