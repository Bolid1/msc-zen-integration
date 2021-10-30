<?php

declare(strict_types=1);

namespace App\Models;

use App\Extensions\EloquentBuilder;
use App\Extensions\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Объединение пользователей ZenMoney.
 *
 * @property positive-int $id Идентификатор группы
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $last_synced_at Дата последней синхронизации
 * @property Integration[]|Collection $integrations Интеграции, связанные с этой группой
 * @property ZenRawItem[]|Collection $items Сущности из ZenMoney
 *
 * @mixin EloquentBuilder
 *
 * @method static ZenGroup firstOrCreate(array $attributes = [], array $values = [])
 * @method static ZenGroup findOrNew($id, $columns = ['*'])
 * @method static EloquentBuilder|ZenGroup newModelQuery()
 * @method static EloquentBuilder|ZenGroup newQuery()
 * @method static EloquentBuilder|ZenGroup query()
 */
class ZenGroup extends Model
{
    protected $casts = [
        'last_synced_at' => 'immutable_datetime',
    ];

    public function integrations(): EloquentBuilder|HasMany
    {
        return $this->hasMany(Integration::class, 'group_id');
    }

    public function items(): EloquentBuilder|HasMany
    {
        return $this->hasMany(ZenRawItem::class, 'group_id');
    }

    public function markSynced(): self
    {
        $this->last_synced_at = new Carbon();

        return $this;
    }
}
