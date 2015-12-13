<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TimeslotsMeeting
 *
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @property integer $id
 * @property string $time_start
 * @property string $time_end
 * @property integer $meeting_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \plunner\Meeting $Meeting
 * @method static \Illuminate\Database\Query\Builder|\plunner\MeetingTimeslot whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\MeetingTimeslot whereTimeStart($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\MeetingTimeslot whereTimeEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\MeetingTimeslot whereMeetingId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\MeetingTimeslot whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\MeetingTimeslot whereUpdatedAt($value)
 */
class MeetingTimeslot extends Model
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
    public function Meeting()
    {
        return $this->belongsTo('plunner\Meeting');
    }
}
