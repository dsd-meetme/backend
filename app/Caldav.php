<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Caldav
 *
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @package plunner
 * @property integer $calendar_id
 * @property string $url
 * @property string $username
 * @property string $password
 * @property string $calendar_name
 * @property string $sync_errors
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \plunner\Calendar $Calendar
 */
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
     * @var array
     */
    protected $fillable = ['url', 'username', 'password', 'calendar_name'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    //TODO remmeber to don't allow to change timelsot for a caldav claendar

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Employee()
    {
        $this->Calendar->Employee();
    }
}
