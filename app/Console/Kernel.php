<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ZenItemsActualizeCommand::class,
        Commands\ZenSyncCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->command(Commands\ZenSyncCommand::class)
            ->everyThirtyMinutes()
            ->withoutOverlapping(7200) // 2 hours
        ;
    }
}
