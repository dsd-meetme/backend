<?php

namespace plunner\Http\Controllers\Employees\Auth;

use Illuminate\Http\Request;
use plunner\Http\Controllers\Controller;
use Tymon\JWTAuth\Support\auth\ResetsPasswords;
use \plunner\Company;
use \plunner\Employee;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords
    {
        postEmail as postEmailOriginal;
        postReset as postResetOriginal;
    }

    /**
     * en = employee normal
     * @var array
     */
    protected $custom = ['mode'=>'en'];

    protected $username = ['email', 'company_id'];

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
        config(['auth.password.table' => 'password_resets_employees']);
    }

    public function postEmail(Request $request)
    {
        $this->validate($request, ['company' => 'required|exists:companies,name']);
        $this->company = Company::whereName($request->input('company'))->firstOrFail();
        $request->merge(['company_id' => $this->company->id]);
        return $this->postEmailOriginal($request);
    }

    public function postReset(Request $request)
    {
        $this->validate($request, ['company' => 'required|exists:companies,name']);
        $this->company = Company::whereName($request->input('company'))->firstOrFail();
        $request->merge(['company_id' => $this->company->id]);
        return $this->postResetOriginal($request);
    }


}
