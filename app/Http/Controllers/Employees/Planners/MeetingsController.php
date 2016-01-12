<?php

namespace plunner\Http\Controllers\Employees\Planners;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use plunner\Group;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests;
use plunner\Http\Requests\Employees\Meeting\MeetingRequest;
use plunner\Meeting;

class MeetingsController extends Controller
{
    public function __construct()
    {
        config(['auth.model' => \plunner\Planner::class]);
        config(['jwt.user' => \plunner\Planner::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    /**
     * Display a listing of the resource.
     * ?current=1 -> to exclude old planed meetings
     *
     * @param int $groupId
     * @param Request $request needed for get query to get only current planed meetings (to be planned are all retrieved)
     * @return mixed
     */
    public function index($groupId, Request $request)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meetings = $group->meetings();
        if ($request->query('current'))
            $meetings->where(function ($query) { //parenthesis for conditions ...(C1 OR C2)...
                $query->where('start_time', '=', NULL);//to be planned
                //datetime to consider timezone, don't use mysql NOW()
                $query->orWhere('start_time', '>=', new \DateTime());//planned
            });

        return $meetings->get();
    }

    /**
     * Display the specified resource.
     *
     * @param int $groupId
     * @param int $meetingId
     * @return mixed
     */
    public function show($groupId, $meetingId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        //Meeting::where('group_id', $groupId)->findOrFail($meetingId);
        //it is good but expensive and useless for the user experience
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        return $meeting;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MeetingRequest $request
     * @param int $groupId
     * @return static
     */
    public function store(MeetingRequest $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $input = $request->all();
        $meeting = $group->meetings()->create($input);
        return $meeting;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MeetingRequest $request
     * @param int $meetingId
     * @param int $groupId
     * @return mixed
     */
    public function update(MeetingRequest $request, $groupId, $meetingId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $input = $request->all();
        //the planner cannot modify the duration of a planed meeting
        if ($meeting->start_time != NULL && $meeting->duration != $input['duration'])
            return Response::json(['error' => 'the meeting is already planned, you cannot change the duration'], 422);
        $meeting->update($input);
        return $meeting;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $groupId
     * @param int $meetingId
     * @return mixed
     */
    public function destroy($groupId, $meetingId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $meeting->delete();
        return $meeting;
    }
}
