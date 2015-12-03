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
    protected $signature = 'sync:caldav {calendarId?} {--background}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync caldav accounts';


    /**
     * @var Schedule laravel schedule object needed to perform command in background
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
            $this->makeForeground(Caldav::findOrFail($calendarId));
        else
            $this->syncAll();
    }

    private function syncAll()
    {
        $calendars = Caldav::all();
        if($this->option('background')) {
            $this->info('Background mode');
            foreach ($calendars as $calendar)
                $this->makeBackground($calendar);
            $this->info('All background tasks started');
        }else
            foreach($calendars as $calendar)
                $this->makeForeground($calendar);


        //TODO check if miss tasks in this way, check the defautl status of garbage

        //TODO log and write all info
    }

    /**
     * sync calendars via exec command
     * @param Caldav $calendar
     */
    private function makeBackground(Caldav $calendar)
    {
        $event = $this->schedule->command('sync:caldav '.$calendar->calendar_id)->withoutOverlapping();
        if($event->isDue($this->laravel))
             $event->run($this->laravel);
    }

    /**
     * sync calendars foreground
     * @param Caldav $calendar
     */
    private function makeForeground(Caldav $calendar)
    {
        $this->info('Sync calendar '. $calendar->calendar_id.' started');
        (new Sync($calendar))->sync();
        $this->info('Sync calendar '. $calendar->calendar_id.' completed');
    }
}
//TODO improvement pool
