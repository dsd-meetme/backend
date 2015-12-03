<?php

namespace plunner\Policies;

use plunner\Group;
use plunner\PolicyCheckable;

/**
 * Class GroupPolicy
 * @package plunner\Policies
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class GroupPolicy
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
     * @param Group $group
     * @return bool
     */
    public function index(PolicyCheckable $policyCheckable, Group $group)
    {
        return $this->userCheck($policyCheckable, $group);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Group $group
     * @return bool
     */
    public function store(PolicyCheckable $policyCheckable, Group $group)
    {
        return $this->userCheck($policyCheckable, $group);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Group $group
     * @return bool
     */
    public function update(PolicyCheckable $policyCheckable, Group $group)
    {
        return $this->userCheck($policyCheckable, $group);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Group $group
     * @return bool
     */
    public function show(PolicyCheckable $policyCheckable, Group $group)
    {
        $ret = $this->userCheck($policyCheckable, $group);
        return $ret;
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Group $group
     * @return bool
     */
    public function destroy(PolicyCheckable $policyCheckable, Group $group)
    {
        return $this->userCheck($policyCheckable, $group);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Group $group
     * @return bool
     */
    private function userCheck(PolicyCheckable $policyCheckable, Group $group)
    {
        return $policyCheckable->verifyGroup($group);
    }
}
