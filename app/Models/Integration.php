<?php

declare(strict_types=1);

namespace App\Models;

use App\Extensions\EloquentBuilder;
use App\Extensions\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Связь между MSC & ZenMoney.
 *
 * @property positive-int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $msc_user_id Идентификатора пользователя MSC, fixme: remove this field
 * @property string $msc_firm_id Идентификатора фирмы MSC
 * @property positive-int $group_id Идентификатор группы {@see ZenGroup::$id}
 * @property ZenGroup $group Группа {@see ZenGroup}
 *
 * @mixin EloquentBuilder
 *
 * @method static Integration updateOrCreate(array $attributes, array $values = [])
 * @method static Integration|null find(int $id)
 * @method static EloquentBuilder|Integration newModelQuery()
 * @method static EloquentBuilder|Integration newQuery()
 * @method static EloquentBuilder|Integration query()
 */
class Integration extends Model
{
    protected $casts = [
        'msc_user_id' => 'string',
        'msc_firm_id' => 'string',
        'group_id' => 'int',
    ];

    protected $fillable = [
        'msc_user_id',
        'msc_firm_id',
        'group_id',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ZenGroup::class, 'group_id');
    }
}
