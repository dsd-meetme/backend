<?php

namespace plunner;

use Illuminate\Database\Eloquent\Model;

/**
 * plunner\Caldav
 *
 * @property integer $calendar_id
 * @property string $url
 * @property string $username
 * @property string $password
 * @property string $calendar_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Calendar $Calendar
 * @method static \Illuminate\Database\Query\Builder|\plunner\Caldav whereCalendarId($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Caldav whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Caldav whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Caldav wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Caldav whereCalendarName($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Caldav whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\plunner\Caldav whereUpdatedAt($value)
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
