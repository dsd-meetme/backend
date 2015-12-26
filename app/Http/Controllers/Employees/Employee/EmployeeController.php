<?php

namespace plunner\Http\Controllers\Employees\Employee;

use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Employees\Employee\EmployeeRequest;


class EmployeeController extends Controller
{
    public function __construct()
    {
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
        $this->middleware('jwt.authandrefresh:mode-en');
    }

    /**
     * Display the employee data
     * /employees/employee/
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = \Auth::user();
        return $employee;
    }


    /**
     * update the employee name and password (both optionally)
     * @param EmployeeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request)
    {
        $employee = \Auth::user();
        $input = $request->only(['name', 'password']);
        $employee->update($input);
        return $employee;
    }


    //TODO test this
}
