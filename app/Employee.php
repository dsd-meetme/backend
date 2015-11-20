<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * plunner\Employee
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property integer $company_id
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \plunner\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Group[] $groups
 * @method static \Illuminate\Database\Query\Builder|\plunner\Employee whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Employee whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Employee whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Employee wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Employee whereCompanyId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Employee whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Employee whereUpdatedAt($value)
 */
class Employee extends Model implements AuthenticatableContract,
                                        AuthorizableContract,
                                        CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    //protected $table = 'employees';

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

    public function company()
    {
        return $this->belongsTo('plunner\Company');
    }

    public function groups()
    {
        return $this->belongsToMany('plunner\Group', 'employee_groups');
    }


    /**
     * Get the e-mail address where password reset links are sent.
     *
     * Make email unique
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email.$this->company->id;
    }
}
