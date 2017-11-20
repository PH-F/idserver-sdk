<?php

namespace Xingo\IDServer\Resources;

use Xingo\IDServer\Concerns\NestedResource;

class Tag extends Resource
{
    use NestedResource;

    /**
     * @param array|string $tag
     * @return array
     */
    public function create($tag)
    {
        $this->call('POST', "users/{$this->parent->id}/tags", [
            'tag' => $tag,
        ]);

        return collect($this->contents['tags'])->all();
    }
}
