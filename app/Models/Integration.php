<?php

declare(strict_types=1);

namespace App\Models;

use App\Extensions\EloquentBuilder;
use App\Extensions\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property Collection|MscToken[] $msc_tokens Токены от MSC
 * @property Collection|ZenResourceResult[] $zen_resource_results Информация о статусе отправке ресурса ZenMoney в MSC
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

    public function msc_tokens(): HasMany
    {
        return $this->hasMany(MscToken::class, 'msc_firm_id', 'msc_firm_id');
    }

    public function zen_resource_results(): EloquentBuilder|HasMany
    {
        return $this->hasMany(
            ZenResourceResult::class,
            'msc_firm_id',
            'msc_firm_id'
        );
    }
}
