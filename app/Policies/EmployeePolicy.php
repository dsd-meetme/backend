<?php

namespace plunner\Policies;

use plunner\Employee;
use plunner\PolicyCheckable;

/**
 * Class EmployeePolicy
 * @package plunner\Policies
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class EmployeePolicy
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
     * @param Employee $employee
     * @return bool
     */
    public function index(PolicyCheckable $policyCheckable, Employee $employee)
    {
        return $this->userCheck($policyCheckable, $employee);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Employee $employee
     * @return bool
     */
    public function store(PolicyCheckable $policyCheckable, Employee $employee)
    {
        return $this->userCheck($policyCheckable, $employee);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Employee $employee
     * @return bool
     */
    public function update(PolicyCheckable $policyCheckable, Employee $employee)
    {
        return $this->userCheck($policyCheckable, $employee);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Employee $employee
     * @return bool
     */
    public function show(PolicyCheckable $policyCheckable, Employee $employee)
    {
        return $this->userCheck($policyCheckable, $employee);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Employee $employee
     * @return bool
     */
    public function destroy(PolicyCheckable $policyCheckable, Employee $employee)
    {
        return $this->userCheck($policyCheckable, $employee);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Employee $employee
     * @return bool
     */
    private function userCheck(PolicyCheckable $policyCheckable, Employee $employee)
    {
        return $policyCheckable->verifyEmployee($employee);
    }
}
