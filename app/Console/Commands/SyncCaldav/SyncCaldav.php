<?php

namespace plunner\Console\Commands\SyncCaldav;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use plunner\Caldav;

/**
 * Class SyncCaldav
 * @package plunner\Console\Commands\SyncCaldav
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class SyncCaldav extends Command
{
    const BACKGROUND_MOD_MEX = 'background mode';
    const BACKGROUND_COMPLETED_MEX = 'All background tasks started';

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
        if (is_numeric($calendarId))
            $this->makeForeground(Caldav::findOrFail($calendarId));
        else
            $this->syncAll();
    }

    /**
     * sync calendars foreground
     * @param Caldav $calendar
     */
    private function makeForeground(Caldav $calendar)
    {
        $this->info('Sync calendar ' . $calendar->calendar_id . ' started');
        (new Sync($calendar))->sync();
        //TODO show errors as warning
        $this->info('Sync calendar ' . $calendar->calendar_id . ' completed');
    }

    private function syncAll()
    {
        //TODO if are there no calendars?
        $calendars = Caldav::all();
        if ($this->option('background')) {
            \Log::debug(self::BACKGROUND_MOD_MEX);
            $this->info(self::BACKGROUND_MOD_MEX);
            foreach ($calendars as $calendar)
                $this->makeBackground($calendar);
            \Log::debug(self::BACKGROUND_COMPLETED_MEX);
            $this->info(self::BACKGROUND_COMPLETED_MEX);
        } else
            foreach ($calendars as $calendar)
                $this->makeForeground($calendar);
    }

    /**
     * sync calendars via exec command
     * @param Caldav $calendar
     */
    private function makeBackground(Caldav $calendar)
    {
        $event = $this->schedule->command('sync:caldav ' . $calendar->calendar_id)->withoutOverlapping();
        if ($event->isDue($this->laravel))
            $event->run($this->laravel);
    }
}
