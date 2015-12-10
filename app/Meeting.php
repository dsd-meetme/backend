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
 */
class Meeting extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'meetings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'start_time',
                    'end_time', 'repeat', 'repetition_end_time', 'is_scheduled', 'group_id', 'employee_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
