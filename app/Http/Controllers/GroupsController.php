<?php

namespace plunner\Http\Controllers;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        if (array_key_exists('employee_name', $input)
            && array_key_exists('group_name', $input))
        {
            try
            {
                $employee = Employee::findOrFail($input['employee_name']);
            }
            catch (ModelNotFoundException $e)
            {
                return $responder::respond(
                    [
                        'message' => 'Employee not found',
                        'employee_name' => $input['employee_name']
                    ],
                    404
                );
            }
            try
            {
                $group = Group::findOrFail($input['group_name']);
            }
            catch (ModelNotFoundException $e)
            {
                return $responder::respond(
                    [
                        'message' => 'Group not found',
                        'group_name' => $input['group_name']
                    ],
                    404
                );
            }
            $group->addEmployee($employee);
        }
        else
        {
            return $responder::respond(
            [
                'message' => 'Specify both name and group'
            ],
            400
        );
        }
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
    }
}
