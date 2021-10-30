<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\Integration;
use App\Models\ZenRawItem;
use App\Models\ZenResourceResult;

class ZenResourceResultsManager
{
    /**
     * Проверяем, создан ли план отправки.
     *
     * @param ZenRawItem $item Строка для актуализации
     */
    public function actualize(ZenRawItem $item): void
    {
        $exists = $item->results;
        $integrationWithoutResult = $item
            ->group
            ->integrations
            ->filter(
                fn (Integration $integration) => null === $exists->firstWhere('msc_firm_id', $integration->msc_firm_id)
            );

        /* @uses ZenResourceResult::resetWhenItemUpdated */
        $exists->each->resetWhenItemUpdated();

        $integrationWithoutResult
            ->each(
                fn (Integration $integration) => ZenResourceResult::fromRawItem(
                    $item,
                    $integration->msc_firm_id
                )->save()
            );

        $item->markSynced()->save();
    }
}
