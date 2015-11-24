<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * plunner\Timeslot
 *
 * @property-read \plunner\Calendar $employees
 */
class Timeslot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['time_start', 'time_end'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employees()
    {
        return $this->belongsTo('plunner\Calendar');
    }
}
