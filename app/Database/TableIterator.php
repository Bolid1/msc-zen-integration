<?php

declare(strict_types=1);

namespace App\Database;

use Countable;
use Generator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use IteratorAggregate;
use function min;

class TableIterator implements IteratorAggregate, Countable
{
    private QueryBuilder|EloquentBuilder $query;
    /** @var int */
    private int $chunkSize;
    /** @var string */
    private string $alias;
    /** @var int */
    private int $minId;
    /** @var int */
    private int  $maxId;
    /** @var int|null */
    private ?int $count;

    public function __construct(
        QueryBuilder|EloquentBuilder $query,
        int $chunkSize,
        string $alias,
        int $minId,
        int $maxId,
        ?int $count = null
    ) {
        $this->query = $query;
        $this->chunkSize = $chunkSize;
        $this->alias = $alias;
        $this->minId = $minId;
        $this->maxId = $maxId;
        $this->count = $count;
    }

    public function getIterator(): Generator
    {
        for ($from = $this->minId; $from <= $this->maxId; $from += $this->chunkSize + 1) {
            $to = min($from + $this->chunkSize, $this->maxId);
            $collection = (clone $this->query)
                ->whereRaw("{$this->alias} BETWEEN {$from} AND {$to}")
                ->get()
            ;

            foreach ($collection as $item) {
                yield $item;
            }
        }
    }

    public function count(): int
    {
        if (!isset($this->count)) {
            $this->count = (int)$this->query->count();
        }

        return $this->count;
    }

    /**
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return int
     */
    public function getMinId(): int
    {
        return $this->minId;
    }

    /**
     * @return int
     */
    public function getMaxId(): int
    {
        return $this->maxId;
    }
}
