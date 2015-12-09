<?php

namespace plunner\Http\Controllers\Employees;

use Illuminate\Http\Request;

use plunner\Http\Requests\Employees\MeetingRequest;
use Carbon\Carbon;
use plunner\Http\Controllers\Controller;
use plunner\Company;
use plunner\Employee;
use plunner\Http\Requests\Companies\Groups\EmployeeRequest;

use plunner\Meeting;

class MeetingsController extends Controller
{
    /**
     * ExampleController constructor.
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $employee = \Auth::user();
        $all_meetings = $employee->meetings;
        return $all_meetings;

        /**
         * Meeting interval repetition (not implemented)
         */
        /*
        *
         * Check for repeating meetings and add them to the collection of meetings.

        foreach ($all_meetings as $meeting) {
            if ($meeting->repeat > 0) {
                $repeat_interval = $meeting->repeat;

                // Determine how much meetings happen in a month based on the month that is currently in view
                $date_start = DateTime::createFromFormat("Y-m-d h:m:s", $meeting->meeting_start);
                if ($date_start.date("m") == $view_month) {
                    $date_start->format("d");
                    $events_remaining = intval($date_start / $repeat_interval);
                }
                else {
                    $events_remaining = intval($view_month->daysInMonth / $repeat_interval);
                }

                // Create new meetings, add the repeat interval and add them to the collection of meetings
                for ($i=1; $i<=$events_remaining; $i++) {
                    $new_meeting = $meeting->replicate();
                    $new_meeting->start_time = $meeting->start_date->addDays($repeat_interval*$i);
                    $new_meeting->end_time = $meeting->end_date->addDays($repeat_interval*$i);
                    $all_meetings += $new_meeting;
                }
            }
        }

        return $all_meetings;*/
    }

    /**
     * Display the specified resource.
     *
     * @param $meetingId
     * @return mixed
     */
    public function show($meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        return $meeting;
    }

}
