<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Timeslot
 *
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @package plunner
 * @property integer $id
 * @property string $time_start
 * @property string $time_end
 * @property integer $calendar_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \plunner\Calendar $calendar
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
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['pivot', 'calendar'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function calendar()
    {
        return $this->belongsTo('plunner\Calendar');
    }
}
