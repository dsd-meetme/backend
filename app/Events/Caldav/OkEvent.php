<?php

namespace plunner\Events\Caldav;

use plunner\Events\Event;
use plunner\Caldav;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OkEvent extends Event
{
    use SerializesModels;

    /**
     * @var Caldav
     */
    private $calendar;

    /**
     * CaldavSyncOkEvent constructor.
     * @param Caldav $calendar
     */
    public function __construct(Caldav $calendar)
    {
        $this->calendar = clone $calendar;
    }

    /**
     * @return Caldav
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
