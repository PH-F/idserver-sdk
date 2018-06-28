<?php

namespace Xingo\IDServer\Entities;

class FeaturedPlan extends Entity
{
    /**
     * @var array
     */
    protected $relationships = [
        'plan_duration' => Duration::class,
    ];

    /**
     * Get all pros of this deal.
     *
     * @return array
     */
    public function pros()
    {
        return $this->getDetailsOfType('pro');
    }

    /**
     * Get all cons of this deal.
     *
     * @return array
     */
    public function cons()
    {
        return $this->getDetailsOfType('con');
    }

    /**
     * Get all details of the given type and sort them by position.
     *
     * @param string $type
     * @return array
     */
    protected function getDetailsOfType(string $type)
    {
        return collect($this->details)
            ->filter(function ($detail) use ($type) {
                return array_get($detail, 'type') === $type;
            })
            ->sortBy('position')
            ->values()
            ->all();
    }
}
