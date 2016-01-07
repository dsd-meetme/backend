<?php

namespace plunner\Listeners\Optimise;

use plunner\Events\Optimise\ErrorEvent;

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
     * @param  ErrorEvent $event
     * @return void
     */
    public function handle(ErrorEvent $event)
    {
        //
        \Log::error('problems during optimise (company id = ' . $event->getCompany()->id . '): ' . $event->getError());
        $company = $event->getCompany();
        //$company = $event->getCompany()->fresh();
        self::sendEmail($company->email, $event->getError());
    }

    /**
     * @param string $email
     * @param string $error
     */
    static private function sendEmail($email, $error)
    {
        \Mail::queue('emails.optimise.error', ['error' => $error], function ($message) use ($email) {
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($email)->subject('Problems during optimisation');
        });
    }
}
