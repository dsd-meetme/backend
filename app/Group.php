<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * plunner\Group
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $planner_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\plunner\Employee[] $employees
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Group whereDescription($value)
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
    protected $fillable = ['name', 'description'];

    public function employees()
    {
        return $this->belongsToMany('plunner\Employee', 'employee_groups');
    }
}
