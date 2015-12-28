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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = \Auth::user();
        return $employee->meetings;
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
