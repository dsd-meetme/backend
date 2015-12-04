<?php

namespace plunner\Listeners;

use plunner\Events\CaldavErrorEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaldavErrorListener
{
    //TODO improvement -> perform this into a queue

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
     * @param  CaldavErrorEvent  $event
     * @return void
     */
    public function handle(CaldavErrorEvent $event)
    {
        //
        \Log::info('problems during caldav (calendar id = '.$event->getCalendar()->calendar_id.') sync: '.$event->getError());
        $event->getCalendar()->sync_errors = $event->getError();
        $event->getCalendar()->save();
    }
}
