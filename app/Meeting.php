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
 */
class Meeting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'meeting_start', 'meeting_end', 'repeat'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany('plunner\Employee');
    }
}
