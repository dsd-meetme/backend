<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Timeslot
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
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
