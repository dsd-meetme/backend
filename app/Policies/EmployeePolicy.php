<?php

namespace plunner\Policies;

use plunner\Company;
use plunner\Employee;

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
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function index(Company $company, Employee $employee)
    {
        return $this->userCheck($company, $employee);
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function store(Company $company, Employee $employee)
    {
        return $this->userCheck($company, $employee);
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function update(Company $company, Employee $employee)
    {
        return $this->userCheck($company, $employee);
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function show(Company $company, Employee $employee)
    {
        $ret = $this->userCheck($company, $employee);
        return $ret;
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function destroy(Company $company, Employee $employee)
    {
        return $this->userCheck($company, $employee);
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    private function userCheck(Company $company, Employee $employee)
    {
        return $company->id === $employee->company_id;
    }
}
