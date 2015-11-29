<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 *
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $company_id
 * @property integer $planner_id
 * @property-read mixed $planner_name
 * @property-read \Illuminate\Database\Eloquent\Collection|Employee[] $employees
 * @property-read Planner $planner
 * @property-read Company $company
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereCompanyId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group wherePlannerId($value)
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
}
