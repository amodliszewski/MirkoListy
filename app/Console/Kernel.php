<?php

namespace App\Console;

use App\Console\Commands\GenerateApiToken;
use App\Console\Commands\RefreshSpamlistUsers;
use App\Console\Commands\Scheduled;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use WykoCommon\Console\Commands\ProcessQueue;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Scheduled::class,
        RefreshSpamlistUsers::class,
        GenerateApiToken::class,
        ProcessQueue::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('scheduled:post')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:process')->everyMinute()->withoutOverlapping();
    }
}
