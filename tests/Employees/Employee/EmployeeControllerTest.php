<?php

namespace Employees\Employee;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class EmployeeControllerTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
    }


    public function testIndex()
    {
        /**
         * @var $company \plunner\Employee
         */
        $employee = \plunner\Employee::findOrFail(1);
        $response = $this->actingAs($employee)->json('GET', '/employees/employee');
        $response->assertResponseOk();
        $response->seeJsonEquals($employee->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/employees/employee');
        $response->seeStatusCode(401);
    }

    public function testUpdate()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $data = [
            'name' => 'test',
            'password' => 'testest',
            'password_confirmation' => 'testest',
        ];

        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/employee/',$data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->seeJson($data2);

        //no correct request
        unset($data['password_confirmation']);
        $response = $this->actingAs($employee)->json('PUT', '/employees/employee/',$data);
        $response->seeStatusCode(422);
    }
}
