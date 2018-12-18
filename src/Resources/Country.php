<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\ResourceBlueprint;

class Country extends Resource
{
    use ResourceBlueprint;

    /**
     * Get the name of the resource to be used in communication with the API.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return 'countries';
    }

    /**
     * @param array $filters
     *
     * @return Collection
     * @throws \Xingo\IDServer\Exceptions\AuthorizationException
     * @throws \Xingo\IDServer\Exceptions\ForbiddenException
     * @throws \Xingo\IDServer\Exceptions\NotFoundException
     * @throws \Xingo\IDServer\Exceptions\ServerException
     * @throws \Xingo\IDServer\Exceptions\ThrottleException
     * @throws \Xingo\IDServer\Exceptions\ValidationException
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->queryString($filters);

        $this->call('GET', 'countries', $query);

        return $this->makeCollection();
    }
}
