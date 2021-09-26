<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Связь между MSC & ZenMoney.
 *
 * @property positive-int $id
 * @property string $msc_user_id Идентификатора пользователя MSC
 * @property string $msc_firm_id Идентификатора фирмы MSC
 * @property positive-int $group_id Идентификатор группы {@see ZenGroup::$id}
 *
 * @mixin Builder
 *
 * @method static Integration updateOrCreate(array $attributes, array $values = [])
 * @method static Builder|Integration newModelQuery()
 * @method static Builder|Integration newQuery()
 * @method static Builder|Integration query()
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
}
