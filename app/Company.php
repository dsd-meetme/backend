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
 * Class Company
 *
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property boolean $verified
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Employee[] $employees
 * @property-read \Illuminate\Database\Eloquent\Collection|Group[] $groups
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Company whereUpdatedAt($value)
 */
class Company extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract,
                                    PolicyCheckable
{
    use Authenticatable, Authorizable, CanResetPassword;

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

    /**
     * @param Calendar $calendar
     * @return bool
     */
    public function verifyCalendar(Calendar $calendar)
    {
        //TODO test this
        return $calendar->employee->company->id == $this->id;
    }

    /**
     * @param Meeting $meeting
     * @return bool
     */
    public function verifyMeeting(Meeting $meeting)
    {
        //TODO implement and test this
        return false;
    }

    /**
     * @param MeetingTimeslot $meetingTimeslot
     * @return bool
     */
    public function verifyMeetingTimeslot(MeetingTimeslot $meetingTimeslot)
    {
        //TODO implement and test this
        return false;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return \Illuminate\Support\Collection
     */
    public function getEmployeesTimeSlots($from, $to)
    {
        return \DB::table('employees')
            ->join('calendars', 'employees.id', '=', 'calendars.employee_id')
            ->join('timeslots', 'calendars.id', '=', 'timeslots.calendar_id')
            ->where('calendars.enabled', '=', '1')
            ->where('timeslots.time_start', '>=', $from)
            ->where('timeslots.time_end', '<=', $to)
            ->where('employees.company_id','=', $this->id)
            ->select('employees.id','timeslots.time_start','timeslots.time_end')
            ->get();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return \Illuminate\Support\Collection
     */
    public function getMeetingsTimeSlots($from, $to)
    {
        return \DB::table('meetings')
            ->join('groups', 'meetings.group_id', '=', 'groups.id')
            ->join('meeting_timeslots', 'meetings.id', '=', 'meeting_timeslots.meeting_id')
            ->where('meeting_timeslots.time_start', '>=', $from)
            ->where('meeting_timeslots.time_end', '<=', $to)
            ->where('groups.company_id','=', $this->id)
            ->select('meetings.id', 'meetings.duration','meeting_timeslots.time_start','meeting_timeslots.time_end')
            ->get();
    }

    public function getUsersMeetings($users, $meetings)
    {
        return \DB::table('meetings')
            ->join('groups', 'meetings.group_id', '=', 'groups.id')
            ->join('employee_group', 'employee_group.group_id', '=', 'groups.id')
            ->join('employees', 'employee_group.employee_id', '=', 'employees.id')
            ->whereIn('employees.id', $users)
            ->whereIn('meetings.id', $meetings)
            ->where('groups.company_id','=', $this->id) //this is not needed
            ->where('employees.company_id','=', $this->id) //this is not needed
            ->select('employees.id as employee_id', 'meetings.id as meeting_id')
            ->get();
    }
}
