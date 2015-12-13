<?php

namespace plunner\Listeners\Optimise;

use plunner\Events\Optimise\OkEvent;
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
    }
}
