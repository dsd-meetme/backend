<?php

namespace plunner;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * plunner\Company
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereUpdatedAt($value)
 * @property string $deleted_at
 * @property boolean $verified
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereVerified($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Employee[] $employees
 */
class Company extends Model implements AuthenticatableContract,
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
    //protected $table = 'users';

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * @param Group $group
     * @return bool
     */
    public function verifyGroup(Group $group)
    {
        return $group->company_id === $this->id;
    }

    /**
     * @param Employee $employee
     * @return bool
     */
    public function verifyEmployee(Employee $employee)
    {
        return $employee->company_id === $this->id;
    }

    /**
     * @param Company $company
     * @return bool
     */
    public function verifyCompany(Company $company)
    {
        return $company->id === $this->id;
    }
}
