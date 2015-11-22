<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class GroupControllerTest extends TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company;
    private $data;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $employee_ids = array_map(function ($employee) {
            return $employee['id'];
        }, array_rand($this->company->employees->toArray(), 2));
        $planner_id = array_rand($employee_ids);

        $this->data = [
            'name' => 'Testers',
            'description' => 'Group for testing stuff',
            'employees' => $employee_ids,
            'planner' => $planner_id,
        ];
    }


    public function testIndexAllGroups()
    {
        $response = $this->actingAs($this->company)->json('GET', '/companies/groups');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->company->groups->toArray());
    }

    public function testErrorIndexNoCompany()
    {
        $response = $this->json('GET', '/companies/groups');

        $response->seeStatusCode(401);
    }

    public function testShowSpecificGroup()
    {
        $group = $this->company->groups->first();

        $response = $this->actingAs($this->company)->json('GET', '/companies/groups/' . $group->id);

        $response->assertResponseOk();
        $response->seeJsonEquals($group->toArray());
    }

    public function testTryToShowOtherCompaniesGroup()
    {
        $group = \plunner\Group::where('company_id', '<>', $this->company->id)->first();

        $response = $this->actingAs($this->company)->json('GET', '/companies/groups/' . $group->id);

        $response->seeStatusCode(403);
    }

    public function testCreateNewGroup()
    {
        $data_response = $this->data;
        unset($data_response['password']);
        unset($data_response['password_confirmation']);

        $response = $this->actingAs($this->company)->json('POST', '/companies/groups', $this->data);

        $response->assertResponseOk();
        $response->seeJson($data_response);
    }

    public function testCreateDuplicateGroup()
    {
        $this->actingAs($this->company)->json('POST', '/companies/groups' , $this->data);
        $response = $this->actingAs($this->company)->json('POST', '/companies/groups', $this->data);

        $response->seeStatusCode(422);
    }

    public function testSameGroupDataDifferentCompanies()
    {
        $company1 = $this->company;
        $company2 = \plunner\Company::findOrFail(2);
        $company1->groups()->create($this->data);

        $this->actingAs($company1)->json('POST', '/companies/employees', $this->data);
        $response = $this->actingAs($company2)->json('POST', '/companies/employees', $this->data);

        $response->assertResponseOk();
    }

    public function testDelete()
    {
        $group_id = $this->company->groups()->first()->id;
        $response = $this->actingAs($this->company)->json('DELETE', '/companies/employees/' . $group_id);
        $response->assertResponseOk();
    }

    public function testDeleteNonExisting()
    {
        $group_id = plunner\Group::where('company_id', '<>', $this->company->id)->first()->id;
        $response = $this->actingAs($this->company)->json('DELETE', '/companies/employees/' . $group_id);
        $response->seeStatusCode(404);
    }
}
