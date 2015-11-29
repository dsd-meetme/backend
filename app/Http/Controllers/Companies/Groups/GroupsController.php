<?php

namespace plunner\Http\Controllers\Companies\Groups;

use plunner\Company;
use plunner\Group;
use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Companies\Groups\GroupRequest;

/**
 * Class GroupsController
 * @package plunner\Http\Controllers\Companies\Groups
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class GroupsController extends Controller
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
        $groups = $company->groups()->with('planner')->get();
        return $groups;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  GroupRequest  $request
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
        $group = Group::with('planner')->findOrFail($id);
        $this->authorize($group);
        return $group;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  GroupRequest  $request
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
        $group = Group::findOrFail($id);
        $this->authorize($group);
        $group->delete();
        return $group;
    }
}
