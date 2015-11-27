<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class Group extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'planner_id'];

    /**
     * @var array
     */
    protected $hidden = ['planner'];

    /**
     * @var array
     */
    protected $appends = ['planner_name'];

    public function getPlannerNameAttribute()
    {
        if(is_object($this->planner) && $this->planner->exists)
            return $this->planner->name;
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
}
