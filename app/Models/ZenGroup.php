<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Объединение пользователей ZenMoney.
 *
 * @property positive-int $id Идентификатор группы
 * @property Carbon|null $last_synced_at Дата последней синхронизации
 *
 * @mixin Builder
 *
 * @method static ZenGroup firstOrCreate(array $attributes = [], array $values = [])
 * @method static ZenGroup findOrNew($id, $columns = ['*'])
 * @method static Builder|ZenGroup newModelQuery()
 * @method static Builder|ZenGroup newQuery()
 * @method static Builder|ZenGroup query()
 */
class ZenGroup extends Model
{
    protected $casts = [
        'last_synced_at' => 'timestamp',
    ];
}
