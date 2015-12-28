<?php

namespace plunner\Http\Controllers\Employees\Planners;

use plunner\Group;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests;
use plunner\Planner;

class GroupsController extends Controller
{
    public function __construct()
    {
        config(['auth.model' => Planner::class]);
        config(['jwt.user' => Planner::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * @var $planner Planner
         */
        $planner = \Auth::user();
        return $planner->groupsManaged()->with('meetings')->get();
        //TODO get only current meetings via a query
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::with('meetings', 'employees')->findOrFail($id);
        $this->authorize($group);
        return $group;
    }
}
