<?php

namespace Xingo\IDServer\Concerns;

trait ResourceOrganizer
{
    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $perPage = 10;

    /**
     * @var int
     */
    protected $includes = null;

    /**
     * @var string
     */
    protected $sortBy;

    /**
     * @var array|string|null
     */
    protected $relations;

    /**
     * @param  int|boolean  $page
     * @param  int  $perPage
     *
     * @return $this
     */
    public function paginate($page = 1, int $perPage = 10)
    {
        // Disable pagination
        if (false === $page) {
            $page = 1;
            $perPage = -1;
        }

        $this->page = $page;
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @param  string|array  $field
     * @param  string  $order
     *
     * @return $this
     */
    public function sort($field, string $order = 'asc')
    {
        if (is_string($field)) {
            $field = [$field => $order];
        }

        $this->sortBy = $this->parseSortQuery($field);

        return $this;
    }

    /**
     * Include the given relationships.
     *
     * @param array|string $relationships
     *
     * @return $this
     */
    public function include($relationships)
    {
        if (is_array($relationships)) {
            $relationships = implode(',', $relationships);
        }

        $this->includes = $relationships;

        return $this;
    }

    /**
     * @return array
     */
    protected function organizerQuery(): array
    {
        return array_merge([
            'page' => $this->page,
            'per_page' => $this->perPage,
        ], $this->sortBy ? [
            'sort' => $this->sortBy,
        ] : []);
    }

    /**
     * @param  array  $fields
     *
     * @return string
     */
    protected function parseSortQuery(array $fields): string
    {
        return collect($fields)->map(function ($order, $field) {
            return 'desc' === strtolower($order) ?
                "-$field" : "+$field";
        })->implode(',');
    }

    public function withRelations($relations = [])
    {
        $this->relations = $relations;
        return $this;
    }

    public function withoutRelations()
    {
        $this->relations = '';
        return $this;
    }
}
