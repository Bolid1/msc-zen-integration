<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\ZenRawItem;
use App\Mutators\ZenDataMutator;
use Illuminate\Support\Facades\Log;
use Throwable;
use function compact;

/**
 * Единая точка для управления {@see ZenRawItem}.
 */
class ZenRawItemsManager
{
    private ZenDataMutator $mutator;

    public function __construct(ZenDataMutator $mutator)
    {
        $this->mutator = $mutator;
    }

    /**
     * Сохраняем одну единицу данных ZenMoney, полученную из API.
     *
     * @param int $groupId {@see ZenRawItem::$group_id}
     * @param string $type {@see ZenRawItem::$type}
     * @param array $item {@see ZenRawItem::$data}
     *
     * @throws Throwable Не удалось сохранить элемент в БД
     */
    public function save(int $groupId, string $type, array $item): void
    {
        $modelOrErrors = $this->mutator->rawModelFromApi($type, $item);
        if ($modelOrErrors instanceof ZenRawItem) {
            $modelOrErrors->group_id = $groupId;

            $uniq = [
                'group_id' => $modelOrErrors->group_id,
                'type' => $modelOrErrors->type,
                'zen_id' => $modelOrErrors->zen_id,
            ];

            if ($model = ZenRawItem::where($uniq)->first()) {
                $model->setActual($modelOrErrors);
            } else {
                $model = $modelOrErrors;
            }

            $model->saveOrFail();
        } else {
            $errors = $modelOrErrors;
            Log::warning(
                'Failed to parse raw ZenMoney item',
                compact('groupId', 'type', 'item', 'errors')
            );
        }
    }
}
