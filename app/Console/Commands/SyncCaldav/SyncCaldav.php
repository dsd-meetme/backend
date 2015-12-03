<?php

namespace plunner\Console\Commands\SyncCaldav;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use plunner\Caldav;

class SyncCaldav extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:caldav {calendarId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync caldav accounts';


    /**
     * @var Schedule
     */
    private $schedule;


    /**
     * Create a new command instance.
     * @param Schedule $schedule
     *
     * @return void
     */
    public function __construct(Schedule $schedule)
    {
        parent::__construct();
        $this->schedule = $schedule;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $calendarId = $this->argument('calendarId');
        if(is_numeric($calendarId))
            $this->makeSequentially(new Sync(Caldav::findOrFail($calendarId)));
        else
            $this->syncAll();
    }

    private function syncAll()
    {
        $function = 'makeSequentially';
        $function = 'makeThreaded';
        if(class_exists('\Thread')) {
            $this->info('Threaded');
            $function = 'makeThreaded';
        }

        $calendars = Caldav::all();
        foreach($calendars as $calendar) {
            $this->$function(new Sync($calendar));
        }

        //TODO check if miss tasks in this way, check the defautl status of garbage

        //TODO log and write all info
    }

    /**
     * sync calendars via thread
     * @param Sync $sync
     */
    private function makeThreaded(Sync $sync)
    {
        //TODO log return of start
        $event = $this->schedule->command('sync:caldav '.$sync->getCalendar()->calendar_id)->withoutOverlapping();
        if($event->isDue($this->laravel))
             $event->run($this->laravel);
    }

    /**
     * sync calendars sequentially
     * @param Sync $sync
     */
    private function makeSequentially(Sync $sync)
    {
        $this->info('Sync calendar '. $sync->getCalendar()->calendar_id.' started');
        $sync->sync();
        $this->info('Sync calendar '. $sync->getCalendar()->calendar_id.' completed');
    }
}
//TODO improvement pool
