<?php

namespace plunner\Listeners\Caldav;

use plunner\Events\Caldav\OkEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OkListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OkEvent  $event
     * @return void
     */
    public function handle(OkEvent $event)
    {
        //
        $calendar = $event->getCalendar();
        //$calendar = $event->getCalendar()->fresh();
        $calendar->sync_errors = '';
        $calendar->save();
    }
}
