<?php

namespace plunner\Http\Controllers\Employees\Employee;

use Illuminate\Http\Request;

use plunner\Group;
use plunner\Employee;
use plunner\Http\Requests;
use plunner\Http\Controllers\Controller;

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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //TODO Validation
        $employee = \Auth::user();
        $input = $request->only(['name', 'password']);
        $employee->update($input);
        return $employee;
    }


    //TODO test this
}
