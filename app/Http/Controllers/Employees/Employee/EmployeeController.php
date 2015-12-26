<?php

namespace plunner\Http\Controllers\Employees\Employee;

use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Employees\Employee\EmployeeRequest;


class EmployeeController extends Controller
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
     * update the employee name and password (optionally)
     * @param EmployeeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request)
    {
        //TODO Validation
        $employee = \Auth::user();
        $input = $request->only(['name', 'password']);
        $employee->update($input);
        return $employee;
    }


    //TODO test this
}
