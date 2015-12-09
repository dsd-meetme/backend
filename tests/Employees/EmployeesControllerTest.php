<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Support\testing\ActingAs;

class EmployeesControllerTest extends TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $employee;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->employee = $this->company->employees()->with('groups')->first();
    }


    public function testIndex()
    {
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/employees/');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->company->employees->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/employees/employees/');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $test_employee = $this->company->employees()->with('groups')->get()->last();
        $response = $this->actingAs($this->employee)
            ->json('GET', '/companies/employees/'.$test_employee->id);

        $response->assertResponseOk();
        $response->seeJsonEquals($test_employee->toArray());
    }

    public function testShowEmployeeNotInSameCompany()
    {
        $test_employee = \plunner\Employee::where('company_id', '<>', $this->company->id)->firstOrFail();
        $response = $this->actingAs($this->employee)
            ->json('GET', '/companies/employees/'.$test_employee->id);
        $response->seeStatusCode(403);
    }

}
