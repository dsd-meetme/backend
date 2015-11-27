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
 * @property-read \Illuminate\Database\Eloquent\Collection|Group[] $groupsManaged
 * @property-read \plunner\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Group[] $groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Meeting[] $meetings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Calendar[] $calendars
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
}
