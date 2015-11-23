<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'meeting_time'];

    public function employees()
    {
        return $this->belongsToMany('plunner\Employee');
    }
}
