<?php

namespace plunner\Http\Controllers\Employees\Groups;

use Illuminate\Http\Request;

use plunner\Group;
use plunner\Employee;
use plunner\Http\Requests;
use plunner\Http\Controllers\Controller;

class GroupsController extends Controller
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
        return $employee->Groups;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::findOrFail($id);
        $this->authorize($group);
        return $group;
    }
}
