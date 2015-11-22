<?php

namespace plunner\Http\Controllers\Companies\Groups;

use Illuminate\Http\Request;
use plunner\Company;
use plunner\Employee;
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
     * @param  int  $groupId
     * @return \Illuminate\Http\Response
     */
    public function index($groupId)
    {
        //
        //TODO remember to use authorize even here
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $groupId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $groupId)
    {
        //
        //TODO remember to use authorize even here
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $groupId
     * @param  int  $employeeId
     * @return \Illuminate\Http\Response
     */
    public function show($groupId, $employeeId)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $groupId
     * @param  int  $employeeId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $groupId, $employeeId)
    {
        //
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
    }
}
