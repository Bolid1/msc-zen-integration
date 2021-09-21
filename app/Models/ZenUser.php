<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Представление пользователя ZenMoney.
 *
 * @property string $zen_user_id Идентификатор пользователя в ZenMoney
 * @property int $group_id Идентификатор группы {@see ZenGroup::$id}
 *
 * @mixin Builder
 *
 * @method static ZenUser firstOrCreate(array $attributes = [], array $values = [])
 * @method static Builder|ZenUser newModelQuery()
 * @method static Builder|ZenUser newQuery()
 * @method static Builder|ZenUser query()
 * @method static Builder|ZenUser where($column, $operator = null, $value = null, $boolean = 'and')
 */
class ZenUser extends Model
{
    protected $table = 'zen_users';

    protected $primaryKey = 'zen_user_id';
    protected $keyType = 'string';

    protected $fillable = [
        'group_id',
        'zen_user_id',
    ];

    protected $casts = [
        'group_id' => 'int',
    ];
}
