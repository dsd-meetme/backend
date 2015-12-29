<?php

namespace plunner\Http\Controllers\Employees\Planners;

use Illuminate\Http\Request;
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
     * ?current=1 -> to exclude old planed meetings
     *
     * @param Request $request needed for get query to get only current planed meetings (to be planned are all retrieved)
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /**
         * @var $planner Planner
         */
        $planner = \Auth::user();
        $groups = $planner->groupsManaged();
        if ($request->query('current'))
            $groups->with(['meetings' => function ($query) {
                $query->where(function ($query) { //parenthesis for conditions ...(C1 OR C2)...
                    $query->where('start_time', '=', NULL);//to be planned
                    //datetime to consider timezone, don't use mysql NOW()
                    $query->orWhere('start_time', '>=', new \DateTime());//planned
                });
            }]);
        else
            $groups->with('meetings');
        return $groups->get();//both planed and to be planned
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
