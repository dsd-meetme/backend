<?php

namespace Companies\Groups;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
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
        $this->employee = $this->company->employees()->with('groups')->first();
    }


    public function testIndex()
    {
        $response = $this->actingAs($this->employee)
            ->json('GET', '/companies/groups');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->company->groups->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/companies/groups');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $group_id = $this->company->groups->first()->id;
        $response = $this->actingAs($this->employee)
            ->json('GET', '/companies/groups/'.$group_id);

        $response->assertResponseOk();
        $response->seeJsonEquals($this->company->groups->first()->toArray());
    }

    public function testShowGroupNotInSameCompany()
    {
        $test_company = \plunner\Company::where('id', '<>', $this->company->id)->firstOrFail();
        $test_group = $test_company->groups->first();
        $response = $this->actingAs($this->employee)
            ->json('GET', '/companies/groups/'.$test_group->id);
        $response->seeStatusCode(403);
    }
}