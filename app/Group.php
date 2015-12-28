<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 *
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @package plunner
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $company_id
 * @property integer $planner_id
 * @property-read mixed $planner_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Employee[] $employees
 * @property-read \plunner\Planner $planner
 * @property-read \plunner\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Meeting[] $meetings
 */
class Group extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'planner_id'];

    /**
     * @var array
     */
    protected $hidden = ['planner', 'pivot'];

    /**
     * @var array
     */
    protected $appends = ['planner_name'];

    public function getPlannerNameAttribute()
    {
        if(is_object($this->planner) && $this->planner->exists)
            return $this->planner->name;
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planner()
    {
        return $this->belongsTo(Planner::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }
}
