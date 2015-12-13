<?php

namespace plunner\Listeners\optimise;

use plunner\Events\optimise\OkEvent;
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
