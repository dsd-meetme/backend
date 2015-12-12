<?php

namespace Employees\Groups;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class GroupsControllerTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $employee;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->employee = $this->company->employees()->has('groups')->with('groups')->firstOrFail();
    }


    public function testIndex()
    {
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/groups');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->employee->groups->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/employees/groups');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $group_id = $this->employee->groups->first()->id;
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/groups/'.$group_id);

        $response->assertResponseOk();
        $response->seeJsonEquals($this->employee->groups->first()->toArray());
    }

    public function testShowGroupNotInSameCompany()
    {
        $test_company = \plunner\Company::where('id', '<>', $this->company->id)->firstOrFail();
        $test_group = $test_company->groups->first();
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/groups/'.$test_group->id);
        $response->seeStatusCode(403);
    }
}
