<?php

namespace plunner\Http\Controllers\Employees\Auth;

use Illuminate\Http\Request;
use plunner\Company;
use plunner\employee;
use Validator;
use plunner\Http\Controllers\Controller;
use Tymon\JWTAuth\Support\auth\AuthenticatesAndRegistersUsers;
use Tymon\JWTAuth\Support\auth\ThrottlesLogins;
use Log;

/**
 * Class AuthController
 * @package plunner\Http\Controllers\Employees\Auth
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
        postRegister as postRegisterOriginal;
        postLogin as postLoginOriginal;
    }
    use ThrottlesLogins;

    protected $redirectPath = "/";

    /**
     * en = employee normal
     * @var array
     */
    protected $custom = ['mode'=>'en'];

    /**
     * @var company
     */
    private $company = null;

    /**
     * Create a new authentication controller instance.
     *
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:employees,email,NULL,id,company_id,'.$this->company->id,
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
        return $this->company->save(new employee([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]));
    }

    public function postRegister(Request $request)
    {
        $this->validate($request, ['company' => 'required|exists:companies,name']);
        $this->company = Company::whereName($request->input('company'))->firstOrFail();
        return $this->postRegisterOriginal($request);
    }


    public function postLogin(Request $request)
    {
        $this->validate($request, ['company' => 'required|exists:companies,name']);
        $this->company = Company::whereName($request->input('company'))->firstOrFail();
        $request->merge(['company_id' => $this->company->id]);
        return $this->postLoginOriginal($request);
    }


}
