<?php

namespace plunner\Http\Controllers\Employees;

use plunner\Http\Controllers\Controller;
use plunner\Http\Requests;
use plunner\Employee;

class EmployeesController extends Controller
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
        return $employee;
    }

    //TODO this is not RESTFUL, but it can be ok

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorize($employee);
        return $employee;
    }

    //TODO why this? when we need this?
}
