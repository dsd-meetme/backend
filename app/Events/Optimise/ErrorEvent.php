<?php

namespace plunner\Events\Optimise;

use Illuminate\Queue\SerializesModels;
use plunner\Company;
use plunner\Events\Event;

class ErrorEvent extends Event
{
    use SerializesModels;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var String
     */
    private $error;

    /**
     * ErrorEvent constructor.
     * @param Company $company
     * @param String $error
     */
    public function __construct(Company $company, $error)
    {
        $this->company = clone $company;
        $this->error = $error;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return String
     */
    public function getError()
    {
        return $this->error;
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
