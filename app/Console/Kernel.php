<?php

namespace plunner\Console;

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
        \plunner\Console\Commands\Inspire::class,
        \plunner\Console\Commands\SyncCaldav\SyncCaldav::class,
        \plunner\Console\Commands\Optimise\OptimiseCommand::class,
        \plunner\Console\Commands\MeetingsList::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('inspire')
        //         ->hourly();
        $schedule->command('sync:caldav --background')->withoutOverlapping()->everyTenMinutes();
        $schedule->command('optimise:meetings --background')->withoutOverlapping()->weekly()->sundays()->at('00:00');
    }
}
