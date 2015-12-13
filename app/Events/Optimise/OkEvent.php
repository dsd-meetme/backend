<?php

namespace plunner\Events\Optimise;

use plunner\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use plunner\Company;

class OkEvent extends Event
{
    use SerializesModels;

    /**
     * @var Company
     */
    private $company;

    /**
     * OkEvent constructor.
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
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
