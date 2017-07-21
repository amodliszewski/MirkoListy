<?php

namespace App\Console;

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
        'App\Console\Commands\Scheduled',
        'App\Console\Commands\RefreshSpamlistUsers',
        'WykoCommon\Console\Commands\ProcessQueue'
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
