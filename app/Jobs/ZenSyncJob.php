<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Integration;
use App\Services\ZenSyncService;

class ZenSyncJob extends Job
{
    private int $integrationId;

    public function __construct($integrationId)
    {
        $this->integrationId = $integrationId;
    }

    public function handle(ZenSyncService $service): void
    {
        if ($integration = Integration::find($this->integrationId)) {
            $service->sync($integration);
        }
    }
}
