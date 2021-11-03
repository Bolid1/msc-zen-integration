<?php

declare(strict_types=1);

namespace App\Models;

use App\Extensions\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Выгруженная из ZenMoney единица данных.
 *
 * @see https://github.com/zenmoney/ZenPlugins/wiki/ZenMoney-API
 *
 * @property positive-int $id Внутренний идентификатор в приложении
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property positive-int $group_id Идентификатор группы
 * @property string $type Тип данных ({@see ZenRawItem::TYPE_*})
 * @property string $zen_id Идентификатор данных в ZenMoney
 * @property Carbon|null $changed_at Когда в ZenMoney произошло изменение
 * @property string $action Тип действия (удаление/создание-обновление)
 * @property array $data Полный массив полученной информации
 * @property Collection|ZenResourceResult[] $results Список к отправке в MSC.
 * @property ZenGroup|null $group {@see ZenGroup}
 * @property bool $need_result Требуется ли подготовить {@see ZenResourceResult} для этой строки
 */
class ZenRawItem extends Model
{
    // Пользовательские
    public const TYPE_ACCOUNT = 'account';
    public const TYPE_TAG = 'tag';
    public const TYPE_MERCHANT = 'merchant';
    public const TYPE_REMINDER = 'reminder';
    public const TYPE_REMINDER_MARKER = 'reminderMarker';
    public const TYPE_TRANSACTION = 'transaction';
    public const TYPE_BUDGET = 'budget';

    // Системные
    public const TYPE_INSTRUMENT = 'instrument';
    public const TYPE_COMPANY = 'company';
    public const TYPE_USER = 'user';

    public const TYPES = [
        self::TYPE_ACCOUNT,
        self::TYPE_TAG,
        self::TYPE_MERCHANT,
        self::TYPE_REMINDER,
        self::TYPE_REMINDER_MARKER,
        self::TYPE_TRANSACTION,
        self::TYPE_BUDGET,
        self::TYPE_INSTRUMENT,
        self::TYPE_COMPANY,
        self::TYPE_USER,
    ];

    /** @var string Элемент был создан или обновлён */
    public const ACTION_CU = 'create-update';
    /** @var string Элемент был удалён */
    public const ACTION_DEL = 'delete';

    public const ACTIONS = [
        self::ACTION_CU,
        self::ACTION_DEL,
    ];

    protected $casts = [
        'group_id' => 'int',
        'type' => 'string',
        'zen_id' => 'string',
        'changed_at' => 'timestamp',
        'data' => 'json',
    ];

    protected $fillable = [
        'type',
        'action',
        'zen_id',
        'changed_at',
        'data',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(ZenResourceResult::class, 'zen_raw_item_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ZenGroup::class, 'group_id');
    }

    public function setActual(self $actual): self
    {
        $this->setRawAttributes(
            $actual->getAttributes() + $this->getAttributes()
        );

        $this->need_result = $this->need_result || $this->wasChanged();

        return $this;
    }

    public function markSynced(): self
    {
        $this->need_result = false;

        return $this;
    }
}
