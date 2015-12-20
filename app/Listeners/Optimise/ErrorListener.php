<?php

namespace plunner\Listeners\Optimise;

use plunner\Events\Optimise\ErrorEvent;
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
        $event->getCompany()->fresh();
        self::sendEmail($event->getCompany()->email);
    }

    /**
     * @param string $email
     */
    static private function sendEmail($email)
    {
        \Mail::queue('emails.optimise.error', ['error' => $event->getError()], function ($message) use($email) {
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($email)->subject('Problems during optimisation');
        });
    }
}
