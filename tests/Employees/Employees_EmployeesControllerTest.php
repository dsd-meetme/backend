<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Support\testing\ActingAs;

class Employees_EmployeesControllerTest extends TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $employee;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->employee = $this->company->employees->first();
    }


    public function testIndex()
    {
        /**
         * @var $company \plunner\Company
         */

        $response = $this->actingAs($this->employee)->
        json('GET', '/companies/'.$this->company->id.'employees/'.$this->employee->id);

        $response->assertResponseOk();
        $response->seeJsonEquals($this->employee->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/companies/'.$this->company->id.'employees/'.$this->employee->id);
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $test_employee = $this->company->employees->where('id', '<>', $this->employee->id)->firstOrFail();
        $response = $this->actingAs($this->employee)
            ->json('GET', '/companies/'.$this->company->id.'employees/'.$test_employee->id);

        $response->assertResponseOk();
        $response->seeJsonEquals($test_employee->toArray());
    }

    public function testShowEmployeeNotInSameCompany()
    {
        $test_employee = \plunner\Employee::where('company_id', '<>', $this->company->id)->firstOrFail();
        $response = $this->actingAs($this->employee)
            ->json('GET', '/companies/'.$test_employee->company->id.'employees/'.$test_employee->id);
        $response->seeStatusCode(403);
    }

}
