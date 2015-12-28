<?php

namespace Companies\Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class GroupControllerTest extends \TestCase
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
        $planner_id = $this->company->employees()->first()->id;

        $this->data = [
            'name' => 'Testers',
            'description' => 'Group for testing stuff',
            'planner_id' => $planner_id,
        ];
    }


    public function testIndexAllGroups()
    {
        $response = $this->actingAs($this->company)->json('GET', '/companies/groups');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->company->groups()->with('planner')->get()->toArray());
    }

    public function testErrorIndexNoCompany()
    {
        $response = $this->json('GET', '/companies/groups');

        $response->seeStatusCode(401);
    }

    public function testShowSpecificGroup()
    {
        $group = $this->company->groups()->with('planner')->first();

        $response = $this->actingAs($this->company)->json('GET', '/companies/groups/' . $group->id);

        $response->assertResponseOk();
        $response->seeJsonEquals($group->toArray());

        //test planner name
        $json = $response->response->content();
        $json = json_decode($json, true);
        $this->assertEquals($group->planner->name, $json['planner_name']);
    }

    public function testTryToShowOtherCompaniesGroup()
    {
        $group = \plunner\Group::where('company_id', '<>', $this->company->id)->firstOrFail();

        $response = $this->actingAs($this->company)->json('GET', '/companies/groups/' . $group->id);

        $response->seeStatusCode(403);
    }

    public function testCreateNewGroup()
    {
        $data_response = $this->data;

        $response = $this->actingAs($this->company)->json('POST', '/companies/groups', $this->data);

        $response->assertResponseOk();
        $response->seeJson($data_response);
    }

    public function testCreateDuplicateGroup()
    {
        $this->actingAs($this->company)->json('POST', '/companies/groups', $this->data);
        $response = $this->actingAs($this->company)->json('POST', '/companies/groups', $this->data);

        $response->seeStatusCode(422);
    }

    public function testSameGroupDataDifferentCompanies()
    {
        $company1 = $this->company;
        $company2 = \plunner\Company::findOrFail(2);

        //insert in company1
        $company2->groups()->create($this->data);

        //insert user in company2
        $response = $this->actingAs($company1)->json('POST', '/companies/groups/', $this->data);
        $response->assertResponseOk();

        //duplicated in company2
        $response = $this->actingAs($company1)->json('POST', '/companies/groups/', $this->data);
        $response->seeStatusCode(422);
    }

    public function testErrorCreateGroup()
    {
        $planner_id = \plunner\Employee::where('company_id', '<>', $this->company->id)->firstOrFail()->id;
        $data_response = $this->data;
        $data_response['planner_id'] = $planner_id;

        $response = $this->actingAs($this->company)->json('POST', '/companies/groups', $data_response);

        $response->seeStatusCode(422);
    }

    public function testDelete()
    {
        $group_id = $this->company->groups()->firstOrFail()->id;
        $response = $this->actingAs($this->company)->json('DELETE', '/companies/groups/' . $group_id);
        $response->assertResponseOk();

        //not exists
        $response = $this->actingAs($this->company)->json('DELETE', '/companies/groups/' . $group_id);
        $response->seeStatusCode(404);
        $response = $this->actingAs($this->company)->json('GET', '/companies/groups/' . $group_id);
        $response->seeStatusCode(404);
    }

    public function testDeleteNotMine()
    {
        $group_id = \plunner\Group::where('company_id', '<>', $this->company->id)->firstOrFail()->id;
        $response = $this->actingAs($this->company)->json('DELETE', '/companies/groups/' . $group_id);
        $response->seeStatusCode(403);
    }

    public function testUpdate()
    {
        $group = $this->company->groups()->firstOrFail();

        //correct request
        $response = $this->actingAs($this->company)->json('PUT', '/companies/groups/' . $group->id, $this->data);
        $response->assertResponseOk();
        $data2 = $this->data;
        $response->seeJson($data2);

        //dame data OK normal update
        $response = $this->actingAs($this->company)->json('PUT', '/companies/groups/' . $group->id, $this->data);
        $response->assertResponseOk();
        $data2 = $this->data;
        $response->seeJson($data2);

        //duplicate group
        $response = $this->actingAs($this->company)->json('PUT', '/companies/groups/' . ($group->id + 1), $this->data);
        $response->seeStatusCode(422);

        //a no my group
        $group2 = \plunner\Group::where('company_id', '<>', $this->company->id)->firstOrFail();
        $data2 = $this->data;
        $data2['name'] = 'Testers2'; //this since we are acting as original company -> see how requests work
        $response = $this->actingAs($this->company)->json('PUT', '/companies/groups/' . $group2->id, $data2);
        $response->seeStatusCode(403);

        //force field
        $data2 = $this->data;
        $data2['name'] = 'Testers2';
        $data2['company_id'] = 2;
        $response = $this->actingAs($this->company)->json('PUT', '/companies/groups/' . $group->id, $data2);
        $response->assertResponseOk();
        $data3 = $data2;
        $json = $response->response->content();
        $json = json_decode($json, true);
        $this->assertNotEquals($data2['company_id'], $json['company_id']); //this for travis problem due to consider 1 as number instead of string
        $this->assertEquals(1, $json['company_id']);
        unset($data3['company_id']);
        $response->SeeJson($data3);
    }
}
