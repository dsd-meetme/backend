<?php

namespace plunner\Http\Controllers\Companies\Employees;

use Illuminate\Http\Request;
use plunner\Company;
use plunner\Employee;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Companies\Employees\EmployeeRequest;


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
        return $company->employees;
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
        $employee = Employee::findOrFail($id);
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
