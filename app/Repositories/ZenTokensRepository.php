<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ZenToken;
use Illuminate\Database\Eloquent\Collection;

/**
 * Логика получения ({@see ZenToken}) из БД.
 */
class ZenTokensRepository
{
    private ZenToken $model;

    public function __construct(ZenToken $model)
    {
        $this->model = $model;
    }

    public function activeByGroupId(int $groupId): Collection
    {
        return $this->model
            /* @uses \App\Models\ZenToken::$status */
            ->where('status', ZenToken::STATUS_ACTIVE)
            /* @uses \App\Models\ZenToken::$group_id */
            ->where('group_id', $groupId)
            ->get()
        ;
    }
}
