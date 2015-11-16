<?php

namespace plunner;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * plunner\User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\plunner\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\User whereUpdatedAt($value)
 * @property string $deleted_at
 * @property boolean $verified
 * @method static \Illuminate\Database\Query\Builder|\plunner\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\User whereVerified($value)
 */
class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
}
