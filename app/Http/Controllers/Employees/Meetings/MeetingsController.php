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
     * @param Request $request needed for get query to get only current planed meetings (to be planned are all retrieved)
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
}
