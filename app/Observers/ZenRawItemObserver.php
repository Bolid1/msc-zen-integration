<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\ActualizeZenResourceResultJob;
use App\Models\ZenRawItem;
use function dispatch;

class ZenRawItemObserver
{
    public function created(ZenRawItem $item): void
    {
        dispatch(new ActualizeZenResourceResultJob($item->id));
    }

    public function updated(ZenRawItem $item): void
    {
        dispatch(new ActualizeZenResourceResultJob($item->id));
    }
}
