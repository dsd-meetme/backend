<?php

namespace plunner\Http\Controllers\Companies\Auth;

use plunner\Http\Controllers\Controller;
use Tymon\JWTAuth\Support\auth\ResetsPasswords;

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

    use ResetsPasswords;

    /**
     * cn = company normal
     * @var array
     */
    protected $custom = ['mode'=>'cn'];

    protected $redirectTo = '/';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\User::class]);
        config(['jwt.user' => \plunner\User::class]);
    }
}
