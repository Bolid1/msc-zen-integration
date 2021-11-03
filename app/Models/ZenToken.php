<?php

declare(strict_types=1);

namespace App\Models;

use App\Extensions\Model;
use Illuminate\Support\Carbon;

/**
 * Представление для токена сервиса ZenMoney.
 *
 * @property positive-int $id Идентификатор токена в приложении
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $group_id Идентификатор группы {@see ZenGroup::$id}
 * @property string $type Тип токена
 * @property Carbon $expires_at В эту дату срок действия токена завершится
 * @property string $access Сам токен
 * @property string $refresh Специальный ключ для получения нового токена
 * @property string $status Статус токена: рабочий/сломан
 * @property string|null $last_error Информация о последней полученной ошибке
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

    public const STATUS_DISABLED = 'disabled';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ERROR = 'error';

    public function deactivate(?string $error = null): self
    {
        $this->status = static::STATUS_ERROR;
        $this->last_error = $error;

        return $this;
    }
}
