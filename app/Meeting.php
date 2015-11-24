<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * plunner\Meeting
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Employee[] $employees
 */
class Meeting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'meeting_time'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany('plunner\Employee');
    }
}
