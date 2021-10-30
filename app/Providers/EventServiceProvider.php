<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\ZenRawItem;
use App\Observers\ZenRawItemObserver;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ZenRawItem::observe(ZenRawItemObserver::class);
    }
}
