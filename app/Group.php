<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsToMany('App\Employee', 'employee_groups');
    }
}
