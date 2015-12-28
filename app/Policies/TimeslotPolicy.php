<?php

namespace plunner\Policies;

use plunner\PolicyCheckable;
use plunner\Timeslot;

/**
 * Class TimeslotPolicy
 * @package plunner\Policies
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class TimeslotPolicy
{
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Timeslot $timeslot
     * @return bool
     */
    public function index(PolicyCheckable $policyCheckable, Timeslot $timeslot)
    {
        return $this->userCheck($policyCheckable, $timeslot);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Timeslot $timeslot
     * @return bool
     */
    private function userCheck(PolicyCheckable $policyCheckable, Timeslot $timeslot)
    {
        return $policyCheckable->verifyTimeslot($timeslot);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Timeslot $timeslot
     * @return bool
     */
    public function store(PolicyCheckable $policyCheckable, Timeslot $timeslot)
    {
        return $this->userCheck($policyCheckable, $timeslot);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Timeslot $timeslot
     * @return bool
     */
    public function update(PolicyCheckable $policyCheckable, Timeslot $timeslot)
    {
        return $this->userCheck($policyCheckable, $timeslot);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Timeslot $timeslot
     * @return bool
     */
    public function show(PolicyCheckable $policyCheckable, Timeslot $timeslot)
    {
        $ret = $this->userCheck($policyCheckable, $timeslot);
        return $ret;
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Timeslot $timeslot
     * @return bool
     */
    public function destroy(PolicyCheckable $policyCheckable, Timeslot $timeslot)
    {
        return $this->userCheck($policyCheckable, $timeslot);
    }
}
