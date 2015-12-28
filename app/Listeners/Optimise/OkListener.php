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
        \Log::info('Meeting correctly optimised (company id = '.$event->getCompany()->id.')');
        $company = $event->getCompany()->fresh();
        //send email to company
        self::sendCompanyEmail($company->email);
        //send emails to employees
        $employees = $company->employees()->with('meetings')->get();
        foreach($employees as $employee)
            self::sendEmployeeEmail($employee->email, $employee->meetings);
    }

    /**
     * @param string $email
     */
    static private function sendCompanyEmail($email)
    {
        \Mail::queue('emails.optimise.ok.company', [], function ($message) use($email) {
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($email)->subject('Meetings optimised');
        });
    }

    /**
     * @param string $email
     * @param \Illuminate\Support\Collection $meetings
     */
    static private function sendEmployeeEmail($email, $meetings)
    {
        \Mail::queue('emails.optimise.ok.employee', ['meetings' => $meetings], function ($message) use($email) {
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($email)->subject('Meetings of next week');
        });
    }
}
