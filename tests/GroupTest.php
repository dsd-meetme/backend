<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GroupTest extends TestCase
{
    use DatabaseTransactions;

    public function addAnEmployeeToAGroup()
    {
        $response = $this->json(
            'POST', '/auth/groups',
            [
                'group_name' => 'Group1',
                ['first_employee_name' => 'Miha Vinko'],
            ]
        );
        $response->seeStatusCode(200);

        Employee::from('employees as e')
            ->join('employee_groups as eg', 'e.id', '=', 'eg.business_id')
            ->join('groups as g', 'g.id', '=', 'eg.category_id')
            ->where('g.name', '=', "Group1")
            ->where('e.name', '=', "Miha Vinko")
            ->get(['g.id', 'e.id']);

        $this->seeInDatabase("employee_groups", ['group_id' => 'test', 'employee_id' => 'test@test.com']);

        //already logged (redirect to main page)
        $response = $this->json('POST', '/auth/register', []);
        $response->seeStatusCode(302);
    }
}
