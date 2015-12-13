<?php

namespace plunner\Listeners\optimise;

use plunner\Events\optimise\ErrorEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ErrorListener
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
     * @param  ErrorEvent  $event
     * @return void
     */
    public function handle(ErrorEvent $event)
    {
        //
        \Log::info('problems during optimise (company id = '.$event->getCompany()->id.'): '.$event->getError());
        //TODO communicate errors
    }
}
