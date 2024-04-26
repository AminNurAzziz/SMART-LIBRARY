<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('reservation:check')->hourly();
        // $schedule->command('reservation:check')->dailyAt('00:00');
        $schedule->command('reservation:check')->everySecond();
        // $schedule->command('reservation:check')->daily();
        // $schedule->command('reservation:check')->everyMinute()->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
