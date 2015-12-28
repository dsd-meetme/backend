<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Meeting
 *
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @package plunner
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $group_id
 * @property string $start_time
 * @property integer $duration
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \plunner\Group $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\MeetingTimeslot[] $timeslots
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Employee[] $employees
 */
class Meeting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'duration'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['pivot'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeslots()
    {
        return $this->hasMany('plunner\MeetingTimeslot');
    }

    /**
     * get employees that partecipate to the meetings.
     * for all employees invited use groups with employees
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }
}
