<?php

declare(strict_types=1);

namespace App\Extensions;

use App\Database\TableIterator;
use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use JetBrains\PhpStorm\ArrayShape;

class EloquentBuilder extends BaseBuilder
{
    #[
        ArrayShape([
            'min' => 'int|null',
            'max' => 'int|null',
            'count' => 'int|null',
        ])
    ]
    public function minMaxCount(string $field): array
    {
        $stat = (array)$this
            ->clone()
            ->getQuery()
            ->selectRaw("MIN({$field}) as min, MAX({$field}) as max, COUNT(*) as count")
            ->first()
        ;

        return [
            'min' => $stat['min'] ?? null,
            'max' => $stat['max'] ?? null,
            'count' => $stat['count'] ?? null,
        ];
    }

    public function createTableIterator(
        int $chunkSize,
        string $alias = 'id',
    ): TableIterator {
        $stat = $this->minMaxCount($alias);

        return new TableIterator(
            $this,
            $chunkSize,
            $alias,
            $stat['min'] ?: 0,
            $stat['max'] ?: 0,
            $stat['count'] ?: 0,
        );
    }
}
