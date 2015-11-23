<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public function employees()
    {
        return $this->belongsTo('plunner\Employee');
    }

    public function timeslots()
    {
        return $this->hasMany('App\Timeslot');
    }
}
