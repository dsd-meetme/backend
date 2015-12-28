<?php

namespace plunner\Http\Controllers\Employees\Auth;

use Illuminate\Http\Request;
use plunner\Company;
use plunner\Http\Controllers\Controller;
use Tymon\JWTAuth\Support\auth\ResetsPasswords;

/**
 * Class PasswordController
 * @package plunner\Http\Controllers\Employees\Auth
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
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

    use ResetsPasswords {
        postEmail as postEmailOriginal;
        postReset as postResetOriginal;
    }

    /**
     * en = employee normal
     * @var array
     */
    protected $custom = ['mode' => 'en'];

    /**
     * @var array
     */
    protected $username = ['email', 'company_id'];

    /**
     * @var company
     */
    private $company = null;

    /**
     * Create a new password controller instance.
     *
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
        config(['auth.password.table' => 'password_resets_employees', 'auth.password.email' => 'emails.employees.password']);
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
