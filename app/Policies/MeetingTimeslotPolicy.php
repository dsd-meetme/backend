<?php

namespace plunner\Policies;

use plunner\MeetingTimeslot;
use plunner\PolicyCheckable;

/**
 * Class MeetingTimeslotPolicy
 * @package plunner\Policies
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class MeetingTimeslotPolicy
{
    /**
     * Create a new policy instance.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param MeetingTimeslot $MeetingTimeslot
     * @return bool
     */
    public function index(PolicyCheckable $policyCheckable, MeetingTimeslot $MeetingTimeslot)
    {
        return $this->userCheck($policyCheckable, $MeetingTimeslot);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param MeetingTimeslot $MeetingTimeslot
     * @return bool
     */
    public function store(PolicyCheckable $policyCheckable, MeetingTimeslot $MeetingTimeslot)
    {
        return $this->userCheck($policyCheckable, $MeetingTimeslot);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param MeetingTimeslot $MeetingTimeslot
     * @return bool
     */
    public function update(PolicyCheckable $policyCheckable, MeetingTimeslot $MeetingTimeslot)
    {
        return $this->userCheck($policyCheckable, $MeetingTimeslot);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param MeetingTimeslot $MeetingTimeslot
     * @return bool
     */
    public function show(PolicyCheckable $policyCheckable, MeetingTimeslot $MeetingTimeslot)
    {
        $ret = $this->userCheck($policyCheckable, $MeetingTimeslot);
        return $ret;
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param MeetingTimeslot $MeetingTimeslot
     * @return bool
     */
    public function destroy(PolicyCheckable $policyCheckable, MeetingTimeslot $MeetingTimeslot)
    {
        return $this->userCheck($policyCheckable, $MeetingTimeslot);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param MeetingTimeslot $MeetingTimeslot
     * @return bool
     */
    private function userCheck(PolicyCheckable $policyCheckable, MeetingTimeslot $MeetingTimeslot)
    {
        return $policyCheckable->verifyMeetingTimeslot($MeetingTimeslot);
    }
}
