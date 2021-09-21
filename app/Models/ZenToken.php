<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Представление для токена сервиса ZenMoney.
 *
 * @property positive-int $id Идентификатор токена в приложении
 * @property string $group_id Идентификатор группы {@see ZenGroup::$id}
 * @property string $type Тип токена
 * @property Carbon $expires_at В эту дату срок действия токена завершится
 * @property string $access Сам токен
 * @property string $refresh Специальный ключ для получения нового токена
 *
 * @mixin Builder
 *
 * @method static ZenToken firstOrCreate(array $attributes = [], array $values = [])
 * @method static Builder|ZenToken newModelQuery()
 * @method static Builder|ZenToken newQuery()
 * @method static Builder|ZenToken query()
 */
class ZenToken extends Model
{
    protected $table = 'zen_tokens';

    protected $fillable = [
        'group_id',
        'type',
        'expires_at',
        'access',
        'refresh',
    ];

    protected $casts = [
        'group_id' => 'int',
        'type' => 'string',
        'expires_at' => 'datetime',
        'access' => 'string',
        'refresh' => 'string',
    ];
}
