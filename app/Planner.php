<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

class Planner extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'planners';

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
