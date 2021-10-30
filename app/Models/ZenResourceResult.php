<?php

declare(strict_types=1);

namespace App\Models;

use App\Extensions\EloquentBuilder;
use App\Extensions\Model;
use Illuminate\Support\Carbon;

/**
 * Информация о статусе отправки ресурса Zenmoney в MSC.
 *
 * @property positive-int $id Внутренний идентификатор в приложении
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $msc_firm_id Идентификатора фирмы MSC
 * @property positive-int $zen_raw_item_id Идентификатор ({@see ZenRawItem::$id})
 * @property string|null $msc_resource_id Идентификатор данных в Msc
 * @property Carbon|null $last_try_at Дата последней попытки отправки данных в MSC
 * @property string|null $last_try_error Если при отправке произошла ошибка - информация об этой ошибке
 *
 * @mixin EloquentBuilder
 *
 * @method static ZenResourceResult firstOrCreate(array $attributes = [], array $values = [])
 * @method static ZenResourceResult findOrNew($id, $columns = ['*'])
 * @method static EloquentBuilder|ZenResourceResult newModelQuery()
 * @method static EloquentBuilder|ZenResourceResult newQuery()
 * @method static EloquentBuilder|ZenResourceResult query()
 * @method static EloquentBuilder|ZenResourceResult where($column, $operator = null, $value = null, $boolean = 'and')
 */
class ZenResourceResult extends Model
{
    protected $casts = [
        'msc_firm_id' => 'string',
        'group_id' => 'int',
        'zen_raw_item_id' => 'int',
        'last_try_at' => 'datetime',
        'last_try_error' => 'string',
    ];

    protected $fillable = [
        'msc_firm_id',
        'zen_raw_item_id',
    ];

    public static function fromRawItem(ZenRawItem $item, string $mscFirmId): self
    {
        return new static([
            'msc_firm_id' => $mscFirmId,
            'zen_raw_item_id' => $item->id,
            // 'msc_resource_id' => null,
            // 'last_try_at' => null,
            // 'last_try_error' => null,
        ]);
    }

    public function resetWhenItemUpdated(): void
    {
        $this->last_try_at = null;
        $this->last_try_error = null;

        $this->save();
    }
}
