<?php

namespace Tests\Stub;

use Illuminate\Database\Eloquent\Model;
use Xingo\IDServer\Contracts\EloquentEntity;

class FakeEloquentModel extends Model implements EloquentEntity
{
}
