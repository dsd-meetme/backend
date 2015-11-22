<?php

namespace plunner\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

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
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function index(Company $company, Group $group)
    {
        return $this->userCheck($company, $employee);
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function store(Company $company, Group $group)
    {
        return $this->userCheck($company, $group);
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function update(Company $company, Group $group)
    {
        return $this->userCheck($company, $group);
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function show(Company $company, Group $group)
    {
        $ret = $this->userCheck($company, $group);
        return $ret;
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    public function destroy(Company $company, Group $group)
    {
        return $this->userCheck($company, $group);
    }

    /**
     * @param Company $company
     * @param Employee $employee
     * @return bool
     */
    private function userCheck(Company $company, Group $group)
    {
        return $company->id === $group->company_id;
    }
}
