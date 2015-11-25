<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * plunner\Calendar
 *
 * @property-read \plunner\Employee $employees
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Timeslot[] $timeslots
 */
class Calendar extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employees()
    {
        return $this->belongsTo('plunner\Employee');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeslots()
    {
        return $this->hasMany('pluner\Timeslot');
    }
}
