<?php

namespace plunner\Events\Optimise;

use Illuminate\Queue\SerializesModels;
use plunner\Company;
use plunner\Events\Event;

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
        $this->company = clone $company;
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
