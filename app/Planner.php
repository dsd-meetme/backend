<?php

namespace plunner;


/**
 * Class Planner
 *
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property integer $company_id
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Group[] $groupsManaged
 * @property-read \plunner\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Group[] $groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Meeting[] $meetings
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Calendar[] $calendars
 * @method static \Illuminate\Database\Query\Builder|\plunner\Planner whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Planner whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Planner whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Planner wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Planner whereCompanyId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Planner whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Planner whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Planner whereUpdatedAt($value)
 */
class Planner extends Employee
{

    public function groupsManaged()
    {
        return $this->HasMany(Group::class);
    }

    /*
    * for a planer employee the policyCheckable methods say if the planer can modify or not that part
    */

    /**
     * @param Group $group
     * @return bool
     */
    public function verifyGroup(Group $group)
    {
        $group = $this->groupsManaged()->where('id', $group->id)->first();

        return (is_object($group) && $group->exists);
    }

    /**
     * @param Employee $employee
     * @return bool
     */
    public function verifyEmployee(Employee $employee)
    {
        $group = $this->groupsManaged()->whereHas('employees',function ($query) use ($employee) {$query->where('employees.id', $employee->id);})->first();

        return (is_object($group) && $group->exists);
    }

    /**
     * @param Company $company
     * @return bool
     */
    public function verifyCompany(Company $company)
    {
        return false;
    }

    /**
     * the employee can see a calendar
     * @param Calendar $calendar
     * @return bool
     */
    public function verifyCalendar(Calendar $calendar)
    {
        //TODO implement and test
        return false;
    }

    /**
     * @param Meeting $meeting
     * @return bool
     */
    public function verifyMeeting(Meeting $meeting)
    {
        //TODO test this
        return $meeting->group->planner_id == $this->id;
    }


    /**
     * @param MeetingTimeslot $meetingTimeslot
     * @return bool
     */
    public function verifyMeetingTimeslot(MeetingTimeslot $meetingTimeslot)
    {
        //TODO test this
        return $this->verifyMeeting($meetingTimeslot->meeting);
    }
}
