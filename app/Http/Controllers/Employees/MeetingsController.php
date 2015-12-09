<?php

namespace plunner\Http\Controllers\Employees;

use Illuminate\Http\Request;

use plunner\Http\Requests\MeetingRequest;
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
        return 'g';
        /*$employee = \Auth::user();
        $all_meetings = $employee->meetings;

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
     * Store a newly created resource in storage.
     *
     * @param MeetingRequest $request
     * @param $groupId
     * @return static
     */
    public function store(MeetingRequest $request)
    {
        $employee = \Auth::user();
        $input = $request->all();
        $group = Group::findOrFail($input['group_id']);

        // Check if the employee is the planner for the group.
        if ($employee->id == $group->planner_id)
        {
            $meeting = $group->meetings()->create($input);
            $employees = $group->employees;
            foreach ($employees as $employee)
            {
                $group->employees()->save($employee);
            }
            return $meeting;
        }
        return Response::json(['error' => 'groupId'],404);
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

    /**
     * Update the specified resource in storage.
     *
     * @param MeetingRequest $request
     * @param $meetingId
     * @param $groupId
     * @return mixed
     */
    public function update(MeetingRequest $request, $meetingId)
    {
        $employee = \Auth::user();
        $meeting = Meeting::findOrFail($meetingId);
        $group = $meeting->group;
        $this->authorize($group);


        // Check if the employee is the planner for the group.

        if ($employee->id == $group->planner_id)
        {
            $input = $request->all();
            $meeting->update($input);
            return $meeting;
        }
        return Response::json(['error' => 'meetingId'],404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $meetingId
     * @param $groupId
     * @return mixed
     */
    public function destroy($meetingId)
    {
        $employee = \Auth::user();
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $group = $meeting->group;
        $this->authorize($group);

        // Check if the employee is the planner for the group.
        if ($employee->id == $group->planner_id)
        {
            $meeting->delete();
            return $employee;
        }
        return Response::json(['error' => 'meetingId'],404);
    }
}