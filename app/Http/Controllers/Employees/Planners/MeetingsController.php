<?php

namespace plunner\Http\Controllers;

use Illuminate\Http\Request;

use plunner\Http\Requests;
use plunner\Http\Requests\Employees\MeetingRequest;

//use plunner\Http\Requests\Employees\MeetingRequest;
//TODO above gives undefined namespace on Employees even though the path is correct

use plunner\Meeting;

class MeetingsController extends Controller
{
    /**
     * ExampleController constructor.
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Planner::class]);
        config(['jwt.user' => \plunner\Planner::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    /**
     * Display a listing of the resource.
     *
     * @param $view_month
     * @return mixed
     */
    public function index($view_month)
    {
        $employee = \Auth::user();
        $all_meetings = $employee->meetings;

        /**
         * Check for repeating meetings and add them to the collection of meetings.
         */
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

        return $all_meetings;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param MeetingRequest $request
     * @param $groupId
     * @return static
     */
    public function store(MeetingRequest $request, $groupId)
    {
        $employee = \Auth::user();
        $group = Group::findOrFail($groupId);
        $this->authorize($group);

        /**
         * Check if the employee is the planner for the group.
         */
        if ($employee->id == $group->planner_id)
        {
            $input = $request->all();
            $meeting = Meeting::create($input);
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
    public function update(MeetingRequest $request, $meetingId, $groupId)
    {
        $employee = \Auth::user();
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $group = Group::findOrFail($groupId);
        $this->authorize($group);

        /**
         * Check if the employee is the planner for the group.
         */
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
    public function destroy($meetingId, $groupId)
    {
        $employee = \Auth::user();
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $group = Group::findOrFail($groupId);
        $this->authorize($group);

        /**
         * Check if the employee is the planner for the group.
         */
        if ($employee->id == $group->planner_id)
        {
            $meeting->delete();
            return $employee;
        }
        return Response::json(['error' => 'meetingId'],404);
    }
}
