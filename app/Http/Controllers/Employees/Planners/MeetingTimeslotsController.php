<?php

namespace plunner\Http\Controllers\Employees\Planners;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use plunner\Group;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests;
use plunner\Http\Requests\Employees\Meeting\MeetingTimeslotRequest;
use plunner\Meeting;
use plunner\MeetingTimeslot;


class MeetingTimeslotsController extends Controller
{
    public function __construct()
    {
        config(['auth.model' => \plunner\Planner::class]);
        config(['jwt.user' => \plunner\Planner::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    /**
     * @param $groupId
     * @param $meetingId
     * @return mixed
     */
    public function index($groupId, $meetingId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);

        if ($meeting->group_id == $groupId)
            return $meeting->timeslots;
        return Response::json(['error' => 'meeting->group_id <> groupId'], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param int $groupId
     * @param int $meetingId
     * @param int $timeslotId
     * @return mixed
     */
    public function show($groupId, $meetingId, $timeslotId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $timeslot = MeetingTimeslot::findOrFail($timeslotId);
        $this->authorize($timeslot);

        if ($meeting->group_id == $groupId && $timeslot->meeting_id == $meetingId)
            return $timeslot;
        return Response::json(['error' => 'meeting->group_id <> groupId || timeslot->meeting_id <> meetingId'], 403);
    }

    /**
     * @param MeetingTimeslotRequest $request
     * @param $groupId
     * @param $meetingId
     * @return mixed
     */
    public function store(MeetingTimeslotRequest $request, $groupId, $meetingId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);

        $input = $request->all();

        if ($meeting->group_id == $groupId) {
            $timeslot = $meeting->timeslots()->create($input);
            return $timeslot;
        }
        return Response::json(['error' => 'meeting->group_id <> groupId'], 403);
    }

    /**
     * @param MeetingTimeslotRequest $request
     * @param $groupId
     * @param $meetingId
     * @param $timeslotId
     */
    public function update(MeetingTimeslotRequest $request, $groupId, $meetingId, $timeslotId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $timeslot = MeetingTimeslot::findOrFail($timeslotId);
        $this->authorize($timeslot);

        $input = $request->all();

        if ($meeting->group_id == $groupId && $timeslot->meeting_id == $meetingId) {
            $timeslot->update($input);
            return $timeslot;
        }
        return Response::json(['error' => 'meeting->group_id <> groupId || timeslot->meeting_id <> meetingId'], 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $groupId
     * @param int $meetingId
     * @param int $timeslotId
     * @return mixed
     */
    public function destroy($groupId, $meetingId, $timeslotId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorize($meeting);
        $timeslot = MeetingTimeslot::findOrFail($timeslotId);
        $this->authorize($timeslot);

        if ($meeting->group_id == $groupId && $timeslot->meeting_id == $meetingId) {
            $timeslot->delete();
            return $timeslot;
        }
        return Response::json(['error' => 'meeting->group_id <> groupId || timeslot->meeting_id <> meetingId'], 403);
    }
}
