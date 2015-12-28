<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Calendar
 *
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @package plunner
 * @property integer $id
 * @property string $name
 * @property integer $employee_id
 * @property boolean $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \plunner\Employee $employee
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Timeslot[] $timeslots
 * @property-read \plunner\Caldav $Caldav
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
        return $this->hasOne(Caldav::class);
    }
}
