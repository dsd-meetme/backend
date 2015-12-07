<?php

namespace plunner\Events;

use plunner\Caldav;
use Illuminate\Queue\SerializesModels;

class CaldavErrorEvent extends Event
{
    use SerializesModels;

    /**
     * @var Caldav
     */
    private $calendar;

    /**
     * @var String
     */
    private $error;

    /**
     * Create a new event instance.
     * @param Caldav $calendar
     * @param String $error
     *
     */
    public function __construct(Caldav $calendar, $error)
    {
        $this->calendar = $calendar;
        $this->error = $error;
    }

    /**
     * @return Caldav
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @return String
     */
    public function getError()
    {
        return $this->error;
    }
}
