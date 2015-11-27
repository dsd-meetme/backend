<?php

namespace plunner\Http\Controllers\Companies\Auth;

use plunner\Http\Controllers\Controller;
use Tymon\JWTAuth\Support\auth\ResetsPasswords;

/**
 * Class PasswordController
 * @package plunner\Http\Controllers\Companies\Auth
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

    use ResetsPasswords;

    /**
     * cn = company normal
     * @var array
     */
    protected $custom = ['mode'=>'cn'];


    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);
        config(['auth.password.table' => 'password_resets_companies']);
    }
}
