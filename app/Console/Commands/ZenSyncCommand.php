<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ZenSyncJob;
use App\Models\Integration;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;
use Throwable;
use function dispatch;

class ZenSyncCommand extends Command
{
    /** {@inheritdoc} */
    protected $signature = 'sync:zen';

    /** {@inheritdoc} */
    protected $description = 'Exchange data with ZenMoney';

    public function handle(): void
    {
        $query = Integration::query();

        $this->line('Query: '.$query->toSql());

        $totalSteps = $query->count();
        $this->line('Total integrations: '.$totalSteps);

        $this->withProgressBar(
            $totalSteps,
            function (ProgressBar $bar) use ($query) {
                $query->chunkById(
                    $this->getChunkSize(),
                    function (Collection $collection) use ($bar) {
                        /** @var Integration $integration */
                        foreach ($collection as $integration) {
                            try {
                                dispatch(new ZenSyncJob($integration->id));
                            } catch (Throwable $exception) {
                                Log::error('Failed to exchange data with ZenMoney', [
                                    'integration' => $integration->attributesToArray(),
                                    'exception' => $exception,
                                ]);
                            } finally {
                                $bar->advance();
                            }
                        }
                    }
                );
            }
        );

        $this->newLine();
        $this->line('Finished');
    }

    private function getChunkSize(): int
    {
        return 100;
    }
}
