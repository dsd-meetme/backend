<?php

namespace Companies\Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class EmployeesControllerTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);
    }


    public function testIndex()
    {
        /**
         * @var $company \plunner\Company
         */
        $company = \plunner\Company::findOrFail(1);
        $response = $this->actingAs($company)->json('GET', '/companies/employees');
        $response->assertResponseOk();
        $response->seeJsonEquals($company->employees()->with('groups.planner')->get()->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/companies/employees');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        /**
         * @var $company \plunner\Company
         */
        $company = \plunner\Company::findOrFail(1);
        $employee = $company->employees()->with('groups.planner')->first();
        $response = $this->actingAs($company)->json('GET', '/companies/employees/' . $employee->id);
        $response->assertResponseOk();
        $response->seeJsonEquals($employee->toArray());

        //a no my employee
        $employee = \plunner\Employee::where('company_id', '<>', $company->id)->firstOrFail();
        $response = $this->actingAs($company)->json('GET', '/companies/employees/' . $employee->id);
        $response->seeStatusCode(403);
    }

    public function testCreate()
    {
        /**
         * @var $company \plunner\Company
         */
        $company = \plunner\Company::findOrFail(1);
        $data = [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'testest',
            'password_confirmation' => 'testest',
        ];

        //correct request
        $response = $this->actingAs($company)->json('POST', '/companies/employees/', $data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->seeJson($data2);

        //duplicate employee
        $response = $this->actingAs($company)->json('POST', '/companies/employees/', $data);
        $response->seeStatusCode(422);

        //force field
        $data['email'] = 'test2@test.com';
        $data['company_id'] = 2;
        $response = $this->actingAs($company)->json('POST', '/companies/employees/', $data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $json = $response->response->content();
        $json = json_decode($json, true);
        $this->assertNotEquals($data['company_id'], $json['company_id']); //this for travis problem due to consider 1 as number instead of string
        $this->assertEquals(1, $json['company_id']);
        unset($data2['company_id']);
        $response->SeeJson($data2);
    }

    public function testSameEmailDifferentCompany()
    {
        /**
         * @var $company1 \plunner\Company
         * @var $company2 \plunner\Company
         */
        $company1 = \plunner\Company::findOrFail(1);
        $company2 = \plunner\Company::findOrFail(2);
        $data = [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'testest',
            'password_confirmation' => 'testest',
        ];

        //insert user in company1
        $company1->employees()->create($data);

        //insert user in company2
        $response = $this->actingAs($company2)->json('POST', '/companies/employees/', $data);
        $response->assertResponseOk();

        //duplicated in company2
        $response = $this->actingAs($company2)->json('POST', '/companies/employees/', $data);
        $response->seeStatusCode(422);
    }

    public function testDelete()
    {
        $company = \plunner\Company::findOrFail(1);
        $employee = $company->employees()->with('groups.planner')->first();
        $id = $employee->id;

        //employee exists
        $response = $this->actingAs($company)->json('GET', '/companies/employees/' . $id);
        $response->assertResponseOk();
        $response->seeJsonEquals($employee->toArray());

        //remove
        $response = $this->actingAs($company)->json('DELETE', '/companies/employees/' . $id);
        $response->assertResponseOk();

        //employee doesn't exist
        $response = $this->actingAs($company)->json('GET', '/companies/employees/' . $id);
        $response->seeStatusCode(404);

        //I cannot remove a removed employee
        $response = $this->actingAs($company)->json('DELETE', '/companies/employees/' . $id);
        $response->seeStatusCode(404);
    }

    public function testDeleteNotMine()
    {
        $company = \plunner\Company::findOrFail(1);
        $employee = \plunner\Employee::where('company_id', '<>', $company->id)->firstOrFail();
        $id = $employee->id;
        $response = $this->actingAs($company)->json('DELETE', '/companies/employees/' . $id);
        $response->seeStatusCode(403);
    }

    public function testUpdate()
    {
        $company = \plunner\Company::findOrFail(1);
        $employee = $company->employees->first();
        $data = [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'testest',
            'password_confirmation' => 'testest',
        ];

        //correct request
        $response = $this->actingAs($company)->json('PUT', '/companies/employees/' . $employee->id, $data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->seeJson($data2);

        //same employee update
        //correct request
        $response = $this->actingAs($company)->json('PUT', '/companies/employees/' . $employee->id, $data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->seeJson($data2);

        //duplicate employee email
        $response = $this->actingAs($company)->json('PUT', '/companies/employees/' . ($employee->id + 1), $data);
        $response->seeStatusCode(422);

        //a no my employee
        $employee2 = \plunner\Employee::where('company_id', '<>', $company->id)->firstOrFail();
        $data['email'] = 'test2@test.com'; //this since we are acting as original company -> see how requests work
        $response = $this->actingAs($company)->json('PUT', '/companies/employees/' . $employee2->id, $data);
        $response->seeStatusCode(403);
        $data['email'] = 'test@test.com';

        //force field
        $data['email'] = 'test2@test.com';
        $data['company_id'] = 2;
        $response = $this->actingAs($company)->json('PUT', '/companies/employees/' . $employee->id, $data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $json = $response->response->content();
        $json = json_decode($json, true);
        $this->assertNotEquals($data['company_id'], $json['company_id']); //this for travis problem due to consider 1 as number instead of string
        $this->assertEquals(1, $json['company_id']);
        unset($data2['company_id']);
        $response->SeeJson($data2);
    }
}
