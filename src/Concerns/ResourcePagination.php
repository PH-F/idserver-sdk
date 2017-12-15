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
     * @return array
     */
    protected function paginationQuery(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
