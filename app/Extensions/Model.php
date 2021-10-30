<?php

declare(strict_types=1);

namespace App\Extensions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use JetBrains\PhpStorm\Pure;

class Model extends BaseModel
{
    #[Pure]
    public function newEloquentBuilder($query): EloquentBuilder
    {
        return new EloquentBuilder($query);
    }
}
