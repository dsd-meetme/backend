<?php

namespace plunner\Policies;

use plunner\Meeting;
use plunner\PolicyCheckable;

/**
 * Class MeetingPolicy
 * @package plunner\Policies
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class MeetingPolicy
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
     * @param Meeting $Meeting
     * @return bool
     */
    public function index(PolicyCheckable $policyCheckable, Meeting $Meeting)
    {
        return $this->userCheck($policyCheckable, $Meeting);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Meeting $Meeting
     * @return bool
     */
    private function userCheck(PolicyCheckable $policyCheckable, Meeting $Meeting)
    {
        return $policyCheckable->verifyMeeting($Meeting);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Meeting $Meeting
     * @return bool
     */
    public function store(PolicyCheckable $policyCheckable, Meeting $Meeting)
    {
        return $this->userCheck($policyCheckable, $Meeting);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Meeting $Meeting
     * @return bool
     */
    public function update(PolicyCheckable $policyCheckable, Meeting $Meeting)
    {
        return $this->userCheck($policyCheckable, $Meeting);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Meeting $Meeting
     * @return bool
     */
    public function show(PolicyCheckable $policyCheckable, Meeting $Meeting)
    {
        $ret = $this->userCheck($policyCheckable, $Meeting);
        return $ret;
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Meeting $Meeting
     * @return bool
     */
    public function showImage(PolicyCheckable $policyCheckable, Meeting $Meeting)
    {
        $ret = $this->userCheck($policyCheckable, $Meeting);
        return $ret;
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Meeting $Meeting
     * @return bool
     */
    public function storeImage(PolicyCheckable $policyCheckable, Meeting $Meeting)
    {
        $ret = $this->userCheck($policyCheckable, $Meeting);
        return $ret;
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Meeting $Meeting
     * @return bool
     */
    public function destroy(PolicyCheckable $policyCheckable, Meeting $Meeting)
    {
        return $this->userCheck($policyCheckable, $Meeting);
    }
}
