<?php

declare(strict_types=1);

namespace App\Models;

use App\Extensions\Model;
use Illuminate\Support\Carbon;

/**
 * Представление пользователя ZenMoney.
 *
 * @property string $zen_user_id Идентификатор пользователя в ZenMoney
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $group_id Идентификатор группы {@see ZenGroup::$id}
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
