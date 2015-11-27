<?php

namespace plunner\Http\Controllers;

use Illuminate\Http\Request;

use plunner\Http\Requests;
use plunner\Http\Controllers\Controller;
use plunner\Company;
use plunner\Employee;
use plunner\Http\Requests\Companies\EmployeeRequest;
use plunner\Meeting;

class MeetingsController extends Controller
{
    /**
     * @var plunner/Employee
     */
    private $user;

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
        return $employee->meetings;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\MeetingRequest $request
     * @param $groupId
     * @return static
     */
    public function store(Requests\MeetingRequest $request, $groupId)
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
     * @param Requests\MeetingRequest $request
     * @param $meetingId
     * @param $groupId
     * @return mixed
     */
    public function update(Requests\MeetingRequest $request, $meetingId, $groupId)
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
