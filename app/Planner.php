<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

class Planner extends Model
{
    private $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function group()
    {
        return $this->belongsTo('plunner\Group');
    }
}
