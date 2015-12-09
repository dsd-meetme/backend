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
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->planner = $this->company->groups()->with('planner')->first()->Planner;
    }


    public function testIndex()
    {
        $response = $this->actingAs($this->planner)
            ->json('GET', '/employees/planners/groups/');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->planner->GroupsManaged->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/employees/planners/groups/');
        $response->seeStatusCode(401);
    }

}
