<?php

namespace plunner\Http\Controllers\Companies\Employees;

use plunner\Company;
use plunner\Employee;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Companies\Employees\EmployeeRequest;

/**
 * Class EmployeesController
 * @package plunner\Http\Controllers\Companies\Employees
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class EmployeesController extends Controller
{
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        /**
         * @var $company Company
         */
        $company = \Auth::user();
        return $company->employees()->with('groups.planner')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  EmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        //
        $company = \Auth::user();
        $input = $request->all();
        if(isset($input['password']))
            $input['password'] = bcrypt($input['password']);
        $employee = $company->employees()->create($input);
        return $employee;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $employee = Employee::with('groups.planner')->findOrFail($id);
        $this->authorize($employee);
        return $employee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EmployeeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, $id)
    {
        //
        $employee = Employee::findOrFail($id);
        $this->authorize($employee);
        $input = $request->all();
        if(isset($input['password']))
            $input['password'] = bcrypt($input['password']);
        $employee->update($input);
        return $employee;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $employee = Employee::findOrFail($id);
        $this->authorize($employee);
        $employee->delete();
        return $employee;
    }
}
