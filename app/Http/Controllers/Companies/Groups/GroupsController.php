<?php

namespace plunner\Http\Controllers\Companies\Groups;

use Illuminate\Http\Request;
use plunner\Company;
use plunner\Employee;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Companies\EmployeeRequest;


class GroupsController extends Controller
{
    // TODO move to other controllers
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        /**
         * @var $company Company
         */
        $company = \Auth::user();
        return $company->groups();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        $company = \Auth::user();
        $input = $request->all();
        $group = $company->groups()->create($input);
        return $group;
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupRequest $request, $id)
    {
        $group = Group::findOrFail($id);
        $this->authorize($group);
        $input = $request->all();
        $group->update($input);
        return $group;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = Employee::findOrFail($id);
        $this->authorize($group);
        $group->delete();
        return $group;
    }
}
