<?php

namespace plunner\Console\Commands;

use Illuminate\Console\Command;
use it\thecsea\caldav_client_adapter\simple_caldav_client\SimpleCaldavAdapter;
use plunner\Caldav;
use \it\thecsea\caldav_client_adapter\EventInterface;

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
        if(class_exists('\Thread'))
            $function = 'makeThreaded';

        $calendars = Caldav::all();
        foreach($calendars as $calendar) {
            $this->$function(new Sync($calendar));
        }
    }

    /**
     * sync calendars via thread
     * @param Sync $sync
     */
    private function makeThreaded(Sync $sync)
    {
        $thread = new WorkerThread($sync);
        $thread->start();
    }

    /**
     * sync calendars sequentially
     * @param Sync $sync
     */
    private function makeSequentially(Sync $sync)
    {
        $sync->sync();
    }
}


if(class_exists('\Thread')) {
    class WorkerThread extends \Thread
    {
        /**
         * @var Sync
         */
        private $sync;

        /**
         * workerThread constructor.
         * @param Sync $sync
         */
        public function __construct(Sync $sync)
        {
            $this->sync = $sync;
        }


        public function run()
        {
            $this->sync();
        }
    }
}

class Sync
{
    /**
     * @var Caldav
     */
    private $calendar;

    /**
     * Sync constructor.
     * @param Caldav $calendar
     */
    public function __construct(Caldav $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * perform the sync
     */
    public function sync()
    {
        //TODO ...
        //TODO report erros to clients
        //TODO fire events on sync
        //TODO errors if return false
        /*$events = $this->getEvents();
        foreach($events as $event)
            print_r($this->parseEvent($event));*/
        $this->syncToTimeSlots();
    }

    /**
     * @return array|\it\thecsea\caldav_client_adapter\EventInterface[]
     * @throws \it\thecsea\caldav_client_adapter\CaldavException
     */
    private function getEvents()
    {
        $caldavClient = new SimpleCaldavAdapter();
        $caldavClient->connect($this->calendar->url, $this->calendar->username, $this->calendar->password);
        $calendars = $caldavClient->findCalendars();
        $caldavClient->setCalendar($calendars[$this->calendar->calendar_name]);
        /**
         * 26 hours before to avoid tiemezone problems and dst problems
         * 30 days after
         */
        return $caldavClient->getEvents(date('Ymd\THis\Z', time()-93600), date('Ymd\THis\Z', time()+2592000));
    }

    private function syncToTimeSlots()
    {
        try
        {
            $events = $this->getEvents();
        }catch (\it\thecsea\caldav_client_adapter\CaldavException $e)
        {
            //TODO fire appropriate event
        }

        /**
         * @var $calendarMain \plunner\Calendar
         */
        $calendarMain = $this->calendar->calendar;

        //remove old timeslots
        $calendarMain->timeslots()->delete();
        foreach($events as $event){
            if(!($event = $this->parseEvent($event)))
                ;//TODO fire appropriate event
            $calendarMain->timeslots()->create($event);
        }
    }

    /**
     * @param EventInterface $event
     * @return \DateTime[]|null
     */
    private function parseEvent(EventInterface $event)
    {
        $pattern = "/^((DTSTART;)|(DTEND;))(.*)\$/m";
        if(preg_match_all($pattern, $event->getData(), $matches)){
            if(!isset($matches[4]) || count($matches[4]) != 2)
                return null;
            $ret = [];
            if($tmp = $this->parseDate($matches[4][0]))
                $ret['time_start'] = $tmp;
            else
                return null;
            if($tmp = $this->parseDate($matches[4][1]))
                $ret['time_end'] = $tmp;
            else
                return null;
            return $ret;
        }
        return null;
    }

    /**
     * @param String $date
     * @return \DateTime|null|false
     */
    private function parseDate($date)
    {
        $pattern = "/^((TZID=)|(VALUE=))(.*):(.*)\$/m";
        if(preg_match_all($pattern, $date, $matches)){
            if($matches[1][0] == 'TZID=')
            {
                return \DateTime::createFromFormat('Ymd\THis', $matches[5][0], new \DateTimeZone($matches[4][0]));
            }else if($matches[1][0] == 'VALUE=' && $matches[4][0] == 'DATE')
            {
                return \DateTime::createFromFormat('Ymd\THis', $matches[5][0].'T000000');
            }
        }
        return null;
    }
}