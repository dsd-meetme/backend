<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Meeting
 *
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $meeting_start
 * @property string $meeting_end
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Employee[] $employees
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereMeetingStart($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereMeetingEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereUpdatedAt($value)
 * @property integer $utc
 * @property integer $repeat
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereUtc($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereRepeat($value)
 * @property integer $group_id
 * @property string $start_time
 * @property integer $duration
 * @property-read Group $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\MeetingTimeslot[] $timeslots
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Meeting whereDuration($value)
 */
class Meeting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'duration'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function timeslots()
    {
        return $this->hasMany('plunner\MeetingTimeslot');
    }
}
