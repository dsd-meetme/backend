<?php

namespace plunner\Http\Controllers\Employees\Planners;

use plunner\Http\Controllers\Controller;
use plunner\Http\Requests;
use plunner\Planner;

class GroupsController extends Controller
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
        //return $planner->groupsManaged()->with('employees')->get();
        return $planner->GroupsManaged;
    }
}
