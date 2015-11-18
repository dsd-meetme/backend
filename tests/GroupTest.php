<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GroupTest extends TestCase
{
    use DatabaseTransactions;

    public function addAnEmployeeToAGroup()
    {
        $employee = 'Miha Vinko';
        $group = 'Group1';

        $request = $this->json(
            'POST', '/auth/groups',
            [
                'group_name' => $group,
                ['first_employee_name' => $employee],
            ]
        );

        $results = Employee::from('employees as e')
            ->join('employee_groups as eg', 'e.id', '=', 'eg.employee_id')
            ->join('groups as g', 'g.id', '=', 'eg.group_id')
            ->where('g.name', '=', $group)
            ->where('e.name', '=', $employee);

        assertNotEmpty($results);
    }

    public function addTwoEmployeesToAGroup()
    {
        $first_employee = 'Miha Vinko';
        $second_employee = 'Emil Sila';
        $group = 'Group1';

        $request = $this->json(
            'POST', '/auth/groups',
            [
                'group_name' => $group,
                ['first_employee_name' => $first_employee],
            ]
        );

        $results = Employee::from('employees as e')
            ->join('employee_groups as eg', 'e.id', '=', 'eg.employee_id')
            ->join('groups as g', 'g.id', '=', 'eg.group_id')
            ->where('g.name', '=', $group)
            ->where(function($query, $first_employee, $second_employee)
            {
                $query->where('e.name', '=', $first_employee)
                    ->orWhere('e.name', '=', $second_employee);
            });

        assertEquals(2, length($results));
        /*assertEquals($first_employee, $results->get(0)->name);
        assertEquals($second_employee, $results->get(1)->name);*/
        assertEquals($group, $results->get(0)->group);
        assertEquals($group, $results->get(1)->group);

    }
}
