<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Calendar
 *
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @property integer $id
 * @property string $name
 * @property integer $employee_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \plunner\Employee $employees
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Timeslot[] $timeslots
 * @method static \Illuminate\Database\Query\Builder|\plunner\Calendar whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Calendar whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Calendar whereEmployeeId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Calendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Calendar whereUpdatedAt($value)
 * @property string $type
 * @property string $sync_errors
 * @property boolean $enabled
 * @property-read \plunner\Employee $employee
 * @property-read Caldav $Caldav
 * @method static \Illuminate\Database\Query\Builder|\plunner\Calendar whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Calendar whereSyncErrors($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Calendar whereEnabled($value)
 */
class Calendar extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'enabled'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('plunner\Employee');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeslots()
    {
        return $this->hasMany('plunner\Timeslot');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function Caldav()
    {
        if($this->type == 'caldav')
            return $this->hasOne(Caldav::class);
        return null;
    }
}
