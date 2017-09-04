<?php

namespace App\Console;

use App\Console\Commands\GenerateCategoryCounts;
use App\Console\Commands\ImportAddresses;
use App\Console\Commands\SyncElasticsearch;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ImportAddresses::class,
        GenerateCategoryCounts::class,
        SyncElasticsearch::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // generate categorie counts at midnight each day
        $schedule->command('generate:counts')->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
