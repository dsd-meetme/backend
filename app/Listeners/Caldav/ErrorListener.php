<?php

namespace plunner\Listeners\Caldav;

use plunner\Events\Caldav\ErrorEvent;

class ErrorListener
{
    //TODO improvement -> perform this into a queue

    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ErrorEvent $event
     * @return void
     */
    public function handle(ErrorEvent $event)
    {
        //
        \Log::info('problems during caldav (calendar id = ' . $event->getCalendar()->calendar_id . ') sync: ' . $event->getError());
        $calendar = $event->getCalendar();
        //$calendar = $event->getCalendar()->fresh();
        $calendar->sync_errors = $event->getError();
        $calendar->save();
    }
}
