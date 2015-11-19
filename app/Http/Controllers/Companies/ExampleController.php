<?php

namespace plunner\Http\Controllers\Companies;

use Illuminate\Http\Request;

use plunner\Http\Requests;
use plunner\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class ExampleController extends Controller
{
    /**
     * @var \plunner\User
     */
    private $user;

    /**
     * ExampleController constructor.
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\User::class]);
        config(['jwt.user' => \plunner\User::class]);
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
        return JWTAuth::getUserModel();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
