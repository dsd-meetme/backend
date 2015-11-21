<?php

namespace plunner\Http\Controllers\Employees;

use Illuminate\Http\Request;

use plunner\Http\Requests;
use plunner\Http\Controllers\Controller;

class GroupsController extends Controller
{
    private $responder;

    public function __create(ReponseCreator $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateGroupUpdates($request);
        $input = $request->all();

        $g = Group::create([
            'name' => $input['group_name'],
            'description' => isset($input['description']) ? $input['description'] : ''
        ]);
        $employees = Employee::whereIn('name', $input['employees'])->get();

        $g->addEmployees($employees);

        return $this->respondOK();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateGroupUpdates($request);
        $input = $request->all();

        $employees = Employee::whereIn('name', $input['employees'])->get();

        try {
            $group = Group::findOrFail($input['group_name']);
        } catch (ModelNotFoundException $e) {
            return $this->responder->respond(
                [
                    'message' => 'Group not found',
                    'group_name' => $input['group_name']
                ],
                404
            );
        }
        $group->addEmployees($employees);

        return $this->respondOK();
    }

    private function validateGroupUpdates($request)
    {
        $this->validate($request, [
            'group_name' => 'required|max:255',
            'employees' => 'required|array',
        ]);
    }

    private function respondOK()
    {
        return $this->responder->respond([], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
