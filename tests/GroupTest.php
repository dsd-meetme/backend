<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use plunner\Employee;

class GroupTest extends TestCase
{
    use DatabaseTransactions;

    public function testAddAnEmployeeToAGroup()
    {
        $employee = 'Miha Vinko';
        $group = 'Group1';

        //add employee to company

        $results = Employee::from('employees as e')
            ->join('employee_groups as eg', 'e.id', '=', 'eg.employee_id')
            ->join('groups as g', 'g.id', '=', 'eg.group_id')
            ->where('g.name', '=', $group)
            ->where('e.name', '=', $employee)
            ->get();

        $this->assertEmpty($results);

        $request = $this->json(
            'POST', '/employees/groups',
            [
                'group_name' => $group,
                'employees' => [$employee],
            ]
        );

        $results = Employee::from('employees as e')
            ->join('employee_groups as eg', 'e.id', '=', 'eg.employee_id')
            ->join('groups as g', 'g.id', '=', 'eg.group_id')
            ->where('g.name', '=', $group)
            ->where('e.name', '=', $employee)
            ->get();

        $this->assertNotEmpty($results);
    }

    public function testAddTwoEmployeesToAGroup()
    {
        $first_employee = 'Miha Vinko';
        $second_employee = 'Emil Sila';
        $group = 'Group1';

        $request = $this->json(
            'POST', '/employees/groups',
            [
                'group_name' => $group,
                'employees' => [$first_employee, $second_employee],
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

        $this->assertEquals(2, length($results));
        /*$this->assertEquals($first_employee, $results[0]->name);
        $this->assertEquals($second_employee, $results[1]->name);*/
        $this->assertEquals($group, $results[0]->group);
        $this->assertEquals($group, $results[1]->group);

    }
}
