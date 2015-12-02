<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

class Caldav extends Model
{
    //TODO fillable and other fields

    /**
     * @var string
     */
    protected $primaryKey = 'calendar_id';

    /**
     * @var array
     */
    protected $touches = ['calendar'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Employee()
    {
        $this->calendar->Employee();
    }
}
