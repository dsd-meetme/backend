<?php

namespace Employees\Planners;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class GroupsControllerTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $planner;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Planner::class]);
        config(['jwt.user' => \plunner\Planner::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->planner = $this->company->groups()->has('planner')->with('planner')->firstOrFail()->Planner;
    }


    public function testIndex()
    {
        $response = $this->actingAs($this->planner)
            ->json('GET', '/employees/planners/groups/');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->planner->GroupsManaged()->with('meetings')->get()->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/employees/planners/groups/');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $group = $this->planner->groupsManaged()->with('meetings', 'employees')->firstOrFail();
        $response = $this->actingAs($this->planner)
            ->json('GET', '/employees/planners/groups/' . $group->id);

        $response->assertResponseOk();
        $response->seeJsonEquals($group->toArray());
    }

    public function testShowGroupNotManagedByMe()
    {
        $group = \plunner\Group::where('planner_id', '<>', $this->planner->id)->first();
        if (!$group) {
            $employee = $this->company->employees()->create([
                'name' => 'test',
                'email' => 'test@test.com',
                'password' => 'testest',
                'password_confirmation' => 'testest',
            ]);
            $group = $this->company->Groups()->create([
                'name' => 'Testers',
                'description' => 'Group for testing stuff',
                'planner_id' => $employee->id,
            ]);
        }
        $response = $this->actingAs($this->planner)
            ->json('GET', '/employees/planners/groups/' . $group->id);
        $response->seeStatusCode(403);
    }
}
