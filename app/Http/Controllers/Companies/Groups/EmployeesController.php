<?php

namespace plunner\Http\Controllers\Companies\Groups;

use Illuminate\Support\Facades\Response;
use plunner\Group;
use plunner\Employee;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Companies\Groups\EmployeeRequest;


class EmployeesController extends Controller
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
        //
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        return $group->employees;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  EmployeeRequest  $request
     * @param  int  $groupId
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request, $groupId)
    {
        //
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $id = $request->all()['id'];
        $group->employees()->attach($id);
        return $group->employees;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $groupId
     * @param  int  $employeeId
     * @return \Illuminate\Http\Response
     */
    public function destroy($groupId, $employeeId)
    {
        //
        $employee = Employee::findOrFail($employeeId);
        $this->authorize($employee);
        $group = Group::findOrFail($groupId);
        if(!$employee->belongsToGroup($group))
            return Response::json(['error' => 'employId <> groupId'],404);
        $employee->groups()->detach($groupId);
        return $group->employees;
    }
}
