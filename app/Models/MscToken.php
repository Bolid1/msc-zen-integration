<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Представление для токена сервиса MSC.
 *
 * @property positive-int $id Идентификатор токена в приложении
 * @property string $msc_user_id Идентификатора пользователя MSC
 * @property string $msc_firm_id Идентификатора фирмы MSC
 * @property string $type Тип токена
 * @property Carbon $expires_at В эту дату срок действия токена завершится
 * @property string $access Сам токен
 * @property string $refresh Специальный ключ для получения нового токена
 *
 * @mixin Builder
 *
 * @method static MscToken firstOrCreate(array $attributes = [], array $values = [])
 * @method static Builder|MscToken newModelQuery()
 * @method static Builder|MscToken newQuery()
 * @method static Builder|MscToken query()
 */
class MscToken extends Model
{
    protected $table = 'msc_tokens';

    protected $fillable = [
        'msc_user_id',
        'msc_firm_id',
        'type',
        'expires_at',
        'access',
        'refresh',
    ];

    protected $casts = [
        'msc_user_id' => 'string',
        'msc_firm_id' => 'string',
        'type' => 'string',
        'expires_at' => 'datetime',
        'access' => 'string',
        'refresh' => 'string',
    ];
}
