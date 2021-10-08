<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Integration;
use App\Services\ZenSyncService;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ZenSyncJob extends Job implements ShouldBeUnique
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

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return (string) $this->integrationId;
    }
}
