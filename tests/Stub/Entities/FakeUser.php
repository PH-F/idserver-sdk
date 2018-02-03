<?php

namespace Tests\Stub\Entities;

use Xingo\IDServer\Entities\Ability;

class FakeUser extends \Xingo\IDServer\Entities\User
{
    /**
     * Fake a relation.
     *
     * @return \Illuminate\Support\Collection
     */
    public function abilities()
    {
        return collect([
            new Ability([
                'name' => 'can.do',
                'title' => 'Test',
                'unique' => uniqid(),
            ])
        ]);
    }
}
