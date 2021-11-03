<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Extensions\EloquentBuilder;
use App\Extensions\Model;

class Repository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function query(): Model|EloquentBuilder
    {
        return $this->model->newQuery();
    }

    public function find($id): ?Model
    {
        return $this->query()->find($id);
    }
}
