<?php

namespace plunner\Http\Controllers\Employees\Planners;

use Illuminate\Http\Request;

use plunner\Http\Requests;
use plunner\Http\Requests\Employees\MeetingRequest;
use plunner\Http\Controllers\Controller;

use plunner\Meeting;
use plunner\Group;

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
     *  @param int $groupId
     * @return mixed
     */
    public function index($groupId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        return $group->meetings;
        //TODO get only current meetings
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
        $meeting = Meeting::where('group_id', $groupId)->findOrFail($meetingId); //TODO WHY??? this is very bad since we are not able to catch errors automatically via laravel (find OrFails automatically gives us the 404)., please revert and leave the laravel automatic response controller -> no response::json(..., 404)
        $this->authorize($meeting);
        return $meeting;
    }

    /*
     * Check if a meeting with this title already exists for this group.
     */
    private function checkTitleAlreadyExists($title, $group)
    {
        return in_array($title, array_map(function($meeting)
        {
            return $meeting['title'];
        },
        $group->meetings->toArray()));
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
        if ($this->checkTitleAlreadyExists($input['title'], $group))//TODO create methods inside model is nto the correct way
        {
            abort(422); //TODO thsi is note the correct way
        }
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
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $input = $request->all();
        if ($this->checkTitleAlreadyExists($input['title'], $group))
        {
            abort(422);
        }
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
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting->delete();
        return $meeting;
    }
}
