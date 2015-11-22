<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Support\testing\ActingAs;

class EmployeesControllerTest extends TestCase
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
        $response->seeJsonEquals($company->employees->toArray());
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
        $employee = $company->employees->first();
        $response = $this->actingAs($company)->json('GET', '/companies/employees/'.$employee->id);
        $response->assertResponseOk();
        $response->seeJsonEquals($employee->toArray());

        //a no my employee
        $employee = \plunner\Employee::where('company_id', '<>', $company->id)->first();
        $response = $this->actingAs($company)->json('GET', '/companies/employees/'.$employee->id);
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
        $response = $this->actingAs($company)->json('POST', '/companies/employees/',$data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->seeJson($data2);

        //duplicate employee
        $response = $this->actingAs($company)->json('POST', '/companies/employees/',$data);
        $response->seeStatusCode(422);

        //force field
        $data['email'] = 'test2@test.com';
        $data['company_id'] = 2;
        $response = $this->actingAs($company)->json('POST', '/companies/employees/',$data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->dontSeeJson([$data['company_id']]);
        $data['company_id'] = '1';
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
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
        $response = $this->actingAs($company2)->json('POST', '/companies/employees/',$data);
        $response->assertResponseOk();

        //duplicated in company2
        $response = $this->actingAs($company2)->json('POST', '/companies/employees/',$data);
        $response->seeStatusCode(422);
    }

    public function testDelete()
    {
        $company = \plunner\Company::findOrFail(1);
        $employee = $company->employees->first();
        $id = $employee->id;

        //employee exists
        $response = $this->actingAs($company)->json('GET', '/companies/employees/'.$id);
        $response->assertResponseOk();
        $response->seeJsonEquals($employee->toArray());

        //remove
        $response = $this->actingAs($company)->json('DELETE', '/companies/employees/'.$id);
        $response->assertResponseOk();

        //employee doesn't exist
        $response = $this->actingAs($company)->json('GET', '/companies/employees/'.$id);
        $response->seeStatusCode(404);

        //I cannot remove a removed employee
        $response = $this->actingAs($company)->json('DELETE', '/companies/employees/'.$id);
        $response->seeStatusCode(404);
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
        $response = $this->actingAs($company)->json('PUT', '/companies/employees/'.$employee->id,$data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->seeJson($data2);

        //duplicate employee
        $response = $this->actingAs($company)->json('PUT', '/companies/employees/'.$employee->id,$data);
        $response->seeStatusCode(422);

        //force field
        $data['email'] = 'test2@test.com';
        $data['company_id'] = 2;
        $response = $this->actingAs($company)->json('PUT', '/companies/employees/'.$employee->id,$data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->dontSeeJson([$data['company_id']]);
        $data['company_id'] = '1';
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->SeeJson($data2);
    }
}
