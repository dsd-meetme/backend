<?php

namespace plunner\Http\Controllers\Companies\Groups;

use Illuminate\Http\Request;
use plunner\Company;
use plunner\Employee;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Companies\EmployeeRequest;
use plunner\Http\Requests\PlannerRequest;


class PlannersController extends Controller
{
    /**
     * @var \plunner\Company
     */
    private $user;

    /**
     * ExampleController constructor.
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);
        $this->middleware('jwt.authandrefresh:mode-cn');
    }


    /**
     * Display a listing of the resource.
     *
     * @param  int  $groupId
     * @return \Illuminate\Http\Response
     */
    public function index($groupId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        return $group->planner;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PlannerRequest $request
     * @param $groupId
     * @return mixed
     */
    public function store(PlannerRequest $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);

        $input = $request->all();
        $planner = $group->planner->create($input);
        return $planner;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PlannerRequest $request
     * @param $groupId
     * @return mixed
     */
    public function update(PlannerRequest $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);

        $input = $request->all();
        $group->planner->update($input);
        return $group;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $groupId
     * @return mixed
     */
    public function destroy($groupId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);

        $group->planner->delete();
        return $group;
    }
}
