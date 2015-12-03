<?php

namespace plunner\Console\Commands\SyncCaldav;

use Illuminate\Console\Command;
use it\thecsea\caldav_client_adapter\simple_caldav_client\SimpleCaldavAdapter;
use \it\thecsea\caldav_client_adapter\EventInterface;
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
        if(class_exists('\Thread')) {
            $this->info('Threaded');
            $function = 'makeThreaded';
        }

        $calendars = Caldav::all();
        foreach($calendars as $calendar) {
            $this->$function(new Sync($calendar));
        }

        //TODO log and write all info
    }

    /**
     * sync calendars via thread
     * @param Sync $sync
     */
    private function makeThreaded(Sync $sync)
    {
        /*$thread = new WorkerThread($sync);
        $thread->start(PTHREADS_INHERIT_NONE);*/
        //TODO log return of start
        //$pool = new \Pool(4, Autoloader::class);
        /* submit a task to the pool */
       // $pool->submit(new WorkerThread($sync->getCalendar()->calendar_id));
        $thread = new WorkerThread($sync->getCalendar()->calendar_id);
        $thread->start(PTHREADS_INHERIT_NONE);
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
