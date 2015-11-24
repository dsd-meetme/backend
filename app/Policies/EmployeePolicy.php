<?php

namespace plunner\Policies;

use plunner\Employee;
use plunner\PolicyCheckable;

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
