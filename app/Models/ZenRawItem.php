<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Выгруженная из ZenMoney единица данных.
 *
 * @see https://github.com/zenmoney/ZenPlugins/wiki/ZenMoney-API
 *
 * @property positive-int $id Внутренний идентификатор в приложении
 * @property positive-int $group_id Идентификатор группы
 * @property string $type Тип данных ({@see ZenRawItem::TYPE_*})
 * @property string $zen_id Идентификатор данных в ZenMoney
 * @property Carbon|null $changed_at Когда в ZenMoney произошло изменение
 * @property string $action Тип действия (удаление/создание-обновление)
 * @property array $data Полный массив полученной информации
 *
 * @mixin Builder
 *
 * @method static ZenRawItem firstOrCreate(array $attributes = [], array $values = [])
 * @method static ZenRawItem findOrNew($id, $columns = ['*'])
 * @method static Builder|ZenRawItem newModelQuery()
 * @method static Builder|ZenRawItem newQuery()
 * @method static Builder|ZenRawItem query()
 * @method static Builder|ZenRawItem where($column, $operator = null, $value = null, $boolean = 'and')
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
}
