<?php

namespace Xingo\IDServer\Concerns;

trait ResourcePagination
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
     * @var string
     */
    protected $orderBy;

    /**
     * @param int $page
     * @param int $perPage
     * @return $this
     */
    public function paginate(int $page = 1, int $perPage = 10)
    {
        $this->page = $page;
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @param string|array $field
     * @param string $order
     */
    public function sort($field, string $order = 'asc')
    {
        if (is_string($field)) {
            $field = [$field => $order];
        }

        $this->orderBy = $this->parseSortQuery($field);
    }

    /**
     * @return array
     */
    protected function paginationQuery(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }

    /**
     * @param array $fields
     * @return string
     */
    protected function parseSortQuery(array $fields): string
    {
        return collect($fields)->map(function ($order, $field) {
            return 'desc' === strtolower($order) ?
                "-$field" : "+$field";
        })->implode(',');
    }
}
