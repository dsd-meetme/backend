<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

class Caldav extends Model
{
    //
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
