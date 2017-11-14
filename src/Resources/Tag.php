<?php

namespace Xingo\IDServer\Resources;

class Tag extends NestedResource
{
    /**
     * @param array|string $tag
     * @return array
     */
    public function create($tag)
    {
        $this->call('POST', "users/{$this->id}/tags", [
            'tag' => $tag,
        ]);

        return collect($this->contents['tags'])->all();
    }
}
