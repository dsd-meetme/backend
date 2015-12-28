<?php

namespace Companies\Groups;

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
        $group = $company->groups->first();
        $response = $this->actingAs($company)->json('GET', '/companies/groups/' . $group->id . '/employees');
        $response->assertResponseOk();
        $response->seeJsonEquals($group->employees->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/companies/groups/employees');
        $response->seeStatusCode(401);
    }

    public function testOkStore()
    {
        $company = \plunner\Company::findOrFail(1);
        $planner = $company->employees()->first();
        $group = $company->groups()->create(['name' => 'test', 'description' => 'descr', 'planner_id' => $planner->id]);

        $employees = $company->employees()->take(2)->get();
        $ids = [];
        foreach ($employees as $employee)
            $ids[] = $employee->id;

        $response = $this->actingAs($company)->json('POST', '/companies/groups/' . $group->id . '/employees/', ['id' => $ids]);
        $response->assertResponseOk();
        $json = $response->response->content();
        $json = json_decode($json, true);
        $newJson = [];
        foreach ($json as $ele)
            $newJson[] = $ele['id'];
        //simulate different order return
        $tmp = $ids[0];
        $ids[0] = $ids[1];
        $ids[1] = $tmp;
        sort($ids);
        sort($newJson);
        $this->assertEquals($ids, $newJson);
    }

    public function testStore422()
    {
        $company = \plunner\Company::findOrFail(1);
        $planner = $company->employees()->first();
        $group = $company->groups()->create(['name' => 'test', 'description' => 'descr', 'planner_id' => $planner->id]);
        $employee = \plunner\Employee::where('company_id', '<>', $company->id)->firstOrFail();

        //test wrong ids
        $ids = [$employee->id];
        $response = $this->actingAs($company)->json('POST', '/companies/groups/' . $group->id . '/employees/', ['id' => $ids]);
        $response->seeStatusCode(422);

        //test empty
        $response = $this->actingAs($company)->json('POST', '/companies/groups/' . $group->id . '/employees/', ['id' => []]);
        $response->seeStatusCode(422);
        $response = $this->actingAs($company)->json('POST', '/companies/groups/' . $group->id . '/employees/', []);
        $response->seeStatusCode(422);
    }

    public function testStore403()
    {
        $company = \plunner\Company::findOrFail(1);
        $group = \plunner\Group::where('company_id', '<>', $company->id)->firstOrFail();

        $employees = $company->employees()->take(2)->get();
        $ids = [];
        foreach ($employees as $employee)
            $ids[] = $employee->id;

        $response = $this->actingAs($company)->json('POST', '/companies/groups/' . $group->id . '/employees/', ['id' => $ids]);
        $response->seeStatusCode(403);
    }


    public function testDelete()
    {
        $company = \plunner\Company::findOrFail(1);
        $group = $company->groups()->first();
        $employee = $group->employees()->first();

        //remove
        $response = $this->actingAs($company)->json('DELETE', '/companies/groups/' . $group->id . '/employees/' . $employee->id);
        $response->assertResponseOk();

        //I cannot remove a removed employee
        $response = $this->actingAs($company)->json('DELETE', '/companies/groups/' . $group->id . '/employees/' . $employee->id);
        $response->seeStatusCode(404);
    }

    public function testDeleteEmployeeNotMine()
    {
        $company = \plunner\Company::findOrFail(1);
        $employee = \plunner\Employee::where('company_id', '<>', $company->id)->firstOrFail();
        $id = $employee->id;
        $response = $this->actingAs($company)->json('DELETE', '/companies/groups/' . $company->groups()->first()->id . '/employees/' . $id);
        $response->seeStatusCode(403);
    }

    public function testDeleteGroupNotMine()
    {
        $company = \plunner\Company::findOrFail(1);
        $group = \plunner\Group::where('company_id', '<>', $company->id)->firstOrFail();
        $employee = $group->employees()->first();
        $response = $this->actingAs($company)->json('DELETE', '/companies/groups/' . $group->id . '/employees/' . $employee->id);
        $response->seeStatusCode(403);
    }

    public function testDelete404()
    {
        $company = \plunner\Company::findOrFail(1);
        $group = $company->groups()->first();
        $ids = [];
        $employees = $group->employees;
        foreach ($employees as $employee) {
            $ids[] = $employee->id;
        }
        $employee = $company->employees()->create(['name' => 'ttt', 'email' => 'testm@test.com', 'password' => bcrypt('ttt')]);
        $response = $this->actingAs($company)->json('DELETE', '/companies/groups/' . $group->id . '/employees/' . $employee->id);
        $response->seeStatusCode(404);
    }
}
