<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Meeting[] $meetings
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Calendar[] $calendars
 */
class Employee extends Model implements AuthenticatableContract,
                                        AuthorizableContract,
                                        CanResetPasswordContract,
                                        PolicyCheckable
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('plunner\Company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('plunner\Group', 'employee_group', 'employee_id'); //needed for planner model
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function meetings()
    {
        return $this->belongsToMany('plunner\Meeting');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function calendars()
    {
        return $this->hasMany('App\Calendar');
    }

    /**
     * Get the e-mail address where password reset links are sent.
     * This is needed for multiple user type login
     *
     * Make email unique
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email.$this->company->id;
    }

    /**
     * @param Group $group
     * @return bool
     */
    public function belongsToGroup(Group $group)
    {
        $group = $this->groups()->where('id', $group->id)->first();
        if(is_object($group) && $group->exists)
            return true;
        return false;
    }

    /**
     * @param Group $group
     * @return bool
     */
    public function verifyGroup(Group $group)
    {
        return false; //$this->belongsToGroup($group);
    }

    /**
     * @param Employee $employee
     * @return bool
     */
    public function verifyEmployee(Employee $employee)
    {
        return false; //$employee->id === $this->id;
    }

    /**
     * @param Company $company
     * @return bool
     */
    public function verifyCompany(Company $company)
    {
        return false; //$company->id === $this->company_id;
    }
}
