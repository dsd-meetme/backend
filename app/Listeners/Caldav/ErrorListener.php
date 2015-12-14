<?php

namespace plunner\Listeners\Caldav;

use plunner\Events\Caldav\ErrorEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  ErrorEvent  $event
     * @return void
     */
    public function handle(ErrorEvent $event)
    {
        //
        \Log::info('problems during caldav (calendar id = '.$event->getCalendar()->calendar_id.') sync: '.$event->getError());
        $event->getCalendar()->fresh();
        $event->getCalendar()->sync_errors = $event->getError();
        $event->getCalendar()->save();
    }
}
