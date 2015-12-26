<?php

namespace plunner\Http\Controllers\Companies\Auth;

use Illuminate\Http\Request;
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

    use AuthenticatesAndRegistersUsers{
        postLogin as postLoginOriginal;
    }
    use ThrottlesLogins;

    protected $redirectPath = "/";

    /**
     * cn = company normal
     * @var array
     */
    protected $custom = ['mode'=>'cn'];

    /**
     * Create a new authentication controller instance.
     *
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
            'name' => 'required|min:1|max:255|unique:companies',
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

    public function postLogin(Request $request)
    {
        //remember me
        $this->validate($request, ['remember' => 'boolean']);//TODO insert required
        if($request->input('remember', false))
        {
            config(['jwt.ttl' =>'43200']); //30 days
            $this->custom = array_merge($this->custom, ['remember'=>'true']);
        }else
            $this->custom = array_merge($this->custom, ['remember'=>'false']);
        return $this->postLoginOriginal($request);
    }


}
