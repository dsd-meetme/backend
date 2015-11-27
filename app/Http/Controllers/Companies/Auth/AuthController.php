<?php

namespace plunner\Http\Controllers\Companies\Auth;

use plunner\Company;
use Validator;
use plunner\Http\Controllers\Controller;
use Tymon\JWTAuth\Support\auth\AuthenticatesAndRegistersUsers;
use Tymon\JWTAuth\Support\auth\ThrottlesLogins;

/**
 * Class AuthController
 * @package plunner\Http\Controllers\Companies\Auth
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $redirectPath = "/";

    /**
     * cn = company normal
     * @var array
     */
    protected $custom = ['mode'=>'cn'];

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255|unique:companies',
            'email' => 'required|email|max:255|unique:companies',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Company
     */
    protected function create(array $data)
    {
        return Company::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
