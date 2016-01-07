<?php

namespace plunner\Listeners\Caldav;

use plunner\Events\Caldav\OkEvent;

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
     * @param  OkEvent $event
     * @return void
     */
    public function handle(OkEvent $event)
    {
        //
        $calendar = $event->getCalendar();
        \Log::info('caldav (calendar id = ' . $calendar->calendar_id . ') correctly synchronized');
        //$calendar = $event->getCalendar()->fresh();
        $calendar->sync_errors = '';
        $calendar->save();
    }
}
