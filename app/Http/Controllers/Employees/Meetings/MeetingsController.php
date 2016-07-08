<?php

namespace plunner\Http\Controllers\Employees\Meetings;

use Illuminate\Http\Request;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests;
use plunner\Meeting;

class MeetingsController extends Controller
{
    public function __construct()
    {
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request needed for get query to get only current planed meetings
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $employee = \Auth::user();
        $meetings = $employee->meetings();
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = Meeting::findOrFail($id);
        $this->authorize($meeting);
        return $meeting;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param int $groupId
     * @param int $meetingId
     * @return static
     */
    public function showImage($groupId, $meetingId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $meeting = Group::findOrFail($meetingId);
        $this->authorize($meeting);
        $ret = self::getImg($meeting);
        if ($ret === false)
            return \Response::json(['error' => 'The file is not uploaded'], 404);
        return (new Response($ret, 200))
            ->header('Content-Type', 'image/jpeg');
    }

    private static function getImg(Meeting $meeting)
    {
        $name = 'meetings/' . $meeting->id;
        if (!\Storage::exists($name))
            return false;
        return \Storage::get($name);
    }
}
