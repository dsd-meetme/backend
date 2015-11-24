<?php

namespace plunner\Http\Controllers\Companies\Groups;

use Illuminate\Http\Request;
use plunner\Company;
use plunner\Employee;
use plunner\Group;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Companies\EmployeeRequest;


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
     * @param $groupId
     * @return mixed
     */
    public function index($groupId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        return $group->employees;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeRequest $request
     * @param $groupId
     * @return mixed
     */
    public function store(EmployeeRequest $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);

        $input = $request->all();
        $employee = $group->employees->create($input);
        return $employee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeRequest $request
     * @param $groupId
     * @param $employeeId
     * @return mixed
     */
    public function update(EmployeeRequest $request, $groupId, $employeeId)
    {
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $employee = Employee::findOrFail($employeeId);
        $this->authorize($employee);

        $input = $request->all();
        $group->$employee->update($input);
        return $group;
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
        $group = Group::findOrFail($groupId);
        $this->authorize($group);
        $employee = Employee::findOrFail($employeeId);
        $this->authorize($employee);

        $group->$employee->delete();
        return $group;
    }
}
