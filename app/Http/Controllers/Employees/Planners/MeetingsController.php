<?php

namespace plunner\Http\Controllers;

use Illuminate\Http\Request;

use plunner\Http\Requests;
use plunner\Http\Requests\Employees\MeetingRequest;

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
