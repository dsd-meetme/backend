<?php

namespace plunner\Http\Controllers\Employees\Planners;

use Illuminate\Http\Request;

use plunner\Http\Requests;
use plunner\Http\Requests\Employees\MeetingRequest;
use plunner\Http\Controllers\Controller;

use plunner\Meeting;

class MeetingTimeslotsController extends Controller
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
     *  @param int $meetingId
     *  @param int $timeslotId
     * @return mixed
     */
    public function index($groupId, $meetingId, $timeslotId)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param int $groupId
     * @param int $meetingId
     *  @param int $timeslotId
     * @return mixed
     */
    public function show($groupId, $meetingId, $timeslotId)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MeetingRequest $request
     * @param int $groupId
     * @param int $meetingId
     * @return static
     */
    public function store(MeetingRequest $request, $groupId, $meetingId)
    {
        //TODO create the right request
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MeetingRequest $request
     * @param int $groupId
     * @param int $meetingId
     * @param int $timeslotId
     * @return mixed
     */
    public function update(MeetingRequest $request, $groupId, $meetingId, $timeslotId)
    {
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

    }
}
