<?php

namespace plunner\Http\Controllers\Employees\Groups;

use Illuminate\Http\Request;

use plunner\Group;
use plunner\Employee;
use plunner\Http\Requests;
use plunner\Http\Controllers\Controller;

class GroupsController extends Controller
{
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = \Auth::user();
        return $employee->groups()->with('meetings')->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //TODo fix tests
        $group = Group::with('meetings')->findOrFail($id);
        $this->authorize($group);
        return $group;
    }
}
