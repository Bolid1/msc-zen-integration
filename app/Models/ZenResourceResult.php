<?php

declare(strict_types=1);

namespace App\Models;

use App\Extensions\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use function mb_substr;

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
 * @property string $status В каком состоянии отправка ресурса в данный момент?
 * @property ZenRawItem|null $zen_raw_item
 */
class ZenResourceResult extends Model
{
    public const STATUS_CREATED = 'created';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

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

    public function zen_raw_item(): BelongsTo
    {
        return $this->belongsTo(ZenRawItem::class, 'zen_raw_item_id');
    }

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

    public function startUpload(): self
    {
        $this->last_try_at = new Carbon();

        return $this;
    }

    public function markUploaded(string $resourceId): self
    {
        $this->last_try_error = null;
        $this->msc_resource_id = $resourceId;

        return $this;
    }

    public function markUploadFailed(string $message): self
    {
        $this->last_try_error = mb_substr($message, 0, 255);

        return $this;
    }

    public function markRawItemNotFound(): self
    {
        $this->last_try_error = 'Raw item not found';

        return $this;
    }

    public function markRawItemMutateFailed(): self
    {
        $this->last_try_error = 'There is no code for mutate item';

        return $this;
    }
}
