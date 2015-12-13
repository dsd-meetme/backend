<?php

namespace plunner\Listeners;

use plunner\Events\CaldavSyncOkEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaldavSyncOkListener
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
     * @param  CaldavSyncOkEvent  $event
     * @return void
     */
    public function handle(CaldavSyncOkEvent $event)
    {
        //
        $event->getCalendar()->sync_errors = '';
        $event->getCalendar()->save();
    }
}
