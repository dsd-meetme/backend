<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Timeslot
 *
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @property integer $id
 * @property string $time_start
 * @property string $time_end
 * @property integer $calendar_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \plunner\Calendar $employees
 * @method static \Illuminate\Database\Query\Builder|\plunner\Timeslot whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Timeslot whereTimeStart($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Timeslot whereTimeEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Timeslot whereCalendarId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Timeslot whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Timeslot whereUpdatedAt($value)
 * @property-read \plunner\Calendar $Calendar
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
    public function Calendar()
    {
        return $this->belongsTo('plunner\Calendar');
    }
}
