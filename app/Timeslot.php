<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['time_start', 'time_end'];

    public function employees()
    {
        return $this->belongsTo('plunner\Calendar');
    }
}
