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
        //one meeting planed new, one meeting planed old, one to be planed
        $group = $this->planner->GroupsManaged()->firstOrFail();
        $group->meetings()->save(factory(\plunner\Meeting::class)->make()); //to be planed
        $group->meetings()->save(factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->add(new \DateInterval('PT100S'))])); // new planed
        $group->meetings()->save(factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->sub(new \DateInterval('PT100S'))])); // old planed
        $response = $this->actingAs($this->planner)
            ->json('GET', '/employees/planners/groups/');

        $response->assertResponseOk();
        $this->planner = $this->planner->fresh();
        $response->seeJsonEquals($this->planner->GroupsManaged()->with('meetings')->get()->toArray());
    }

    public function testIndexCurrent()
    {
        //one meeting planed new, one meeting planed old, one to be planed
        $group = $this->planner->GroupsManaged()->firstOrFail();
        $group->meetings()->save(factory(\plunner\Meeting::class)->make()); //to be planed
        $new = factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->add(new \DateInterval('PT100S'))]);
        $group->meetings()->save($new); // new planed
        $old = factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->sub(new \DateInterval('PT100S'))]);
        $group->meetings()->save($old); // old planed

        //other planner meeting planned to test or condition
        $groupOther = \plunner\Group::where('planner_id', '<>', $this->planner->id)->firstOrFail();
        $other = factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->add(new \DateInterval('PT100S'))]);
        $groupOther->meetings()->save($other);
        $response = $this->actingAs($this->planner)
            ->json('GET', '/employees/planners/groups/?current=1');

        $response->assertResponseOk();
        $this->planner = $this->planner->fresh();
        $response->seeJsonEquals($this->planner->GroupsManaged()->with(['meetings' => function ($query) {
            $query->where(function ($query) {
                $query->where('start_time', '=', NULL);//to be planned
                $query->orWhere('start_time', '>=', new \DateTime());//planned
            });
        }])->get()->toArray());
        $content = $response->response->content();
        $content = json_decode($content, true);
        $content = collect($content);
        $content = $content->pluck('meetings')->collapse()->pluck('id')->toArray();
        $this->assertFalse(in_array($old->id, $content));
        $this->assertTrue(in_array($new->id, $content));
        $this->assertFalse(in_array($other->id, $content));
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
