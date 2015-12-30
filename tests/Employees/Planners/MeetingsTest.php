<?php

namespace Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class PlannersMeetingsTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $group, $employee, $planner, $data;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Planner::class]);
        config(['jwt.user' => \plunner\Planner::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->employee = $this->company->employees()->has('groups')->with('groups')->firstOrFail();
        $this->group = $this->employee->groups->first();
        $this->planner = $this->group->planner;

        $this->data = [
            'title' => 'Test meeting',
            'description' => 'Errare humanum est!',
            'duration' => 120
        ];
    }

    public function testCreateMeeting()
    {
        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/' . $this->group->id . '/meetings', $this->data);

        $response->assertResponseOk();
        $response->seeJson($this->data);
    }

    public function testIndexAllMeetings()
    {
        $this->group->meetings()->save(factory(\plunner\Meeting::class)->make()); //to be planed
        $this->group->meetings()->save(factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->add(new \DateInterval('PT100S'))])); // new planed
        $this->group->meetings()->save(factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->sub(new \DateInterval('PT100S'))])); // old planed
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $this->group->id . '/meetings');

        $response->assertResponseOk();
        $this->group = $this->group->fresh();
        $response->seeJsonEquals($this->group->meetings->toArray());
    }

    public function testIndexCurrent()
    {
        //one meeting planed new, one meeting planed old, one to be planed
        $group = factory(\plunner\Group::class)->make(['planner_id' => $this->planner->id]);
        $this->company->groups()->save($group);
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
            ->json('GET', '/employees/planners/groups/' . $group->id . '/meetings/?current=1');

        $response->assertResponseOk();
        $group = $group->fresh();
        $response->seeJsonEquals($group->meetings()->where(function ($query) {
            $query->where('start_time', '=', NULL);//to be planned
            $query->orWhere('start_time', '>=', new \DateTime());//planned
        })->get()->toArray());
        $content = $response->response->content();
        $content = json_decode($content, true);
        $content = collect($content);
        $content = $content->pluck('id')->toArray();
        $this->assertFalse(in_array($old->id, $content));
        $this->assertTrue(in_array($new->id, $content));
        $this->assertFalse(in_array($other->id, $content));
    }


    public function testErrorIndexNoMeetings()
    {
        $response = $this->json('GET', 'employees/planners/groups/' . $this->group->id . '/meetings');

        $response->seeStatusCode(401);
    }

    public function testShowMeeting()
    {
        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/' . $this->group->id . '/meetings', $this->data);
        $meeting_id = $this->group->meetings->first()->id;

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $meeting_id);
        $response->assertResponseOk();
        $response->seeJsonEquals($this->group->meetings()->with('group')->first()->toArray());
    }

    public function testShowNonExistingMeeting()
    {
        $non_existing_meeting_id = $this->getNonExistingMeetingId();

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $non_existing_meeting_id);
        $response->seeStatusCode(404);
    }

    private function getNonExistingMeetingId()
    {
        $test_meeting = \plunner\Meeting::orderBy('id', 'desc')->first();
        $non_existing_meeting_id = $test_meeting->id + 1;
        return $non_existing_meeting_id;
    }

    public function testShowOtherGroupsMeeting()
    {
        $other_group = \plunner\Group::where('planner_id', '<>', $this->planner->id)->first();
        $other_groups_meeting_id = $other_group->meetings()->first()->id;

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $other_group->id . '/meetings/' . $other_groups_meeting_id);
        $response->seeStatusCode(403);
    }

    public function testPlannerDeleteMeeting()
    {
        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/' . $this->group->id . '/meetings', $this->data);
        $meeting_id = $this->group->meetings()->first()->id;

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $meeting_id);
        $response->assertResponseOk();
    }

    public function testEmployeeDeleteMeeting()
    {
        list($test_group, $test_employee) = $this->getNonPlannerInAGroup();

        $meeting_id = $test_group->meetings()->first()->id;

        $response = $this->actingAs($test_employee)
            ->json('DELETE', 'employees/planners/groups/' . $test_group->id . '/meetings/' . $meeting_id);
        $response->seeStatusCode(404);
    }

    private function getNonPlannerInAGroup()
    {
        $group = \plunner\Group::has('employees', '>=', '2')
            ->whereHas('employees', function ($query) {
                $query->whereNotIn('id', \plunner\Planner::all()->pluck('id')); //TODO do in a better way less expensive
            })->firstOrFail();
        $employee = $group->employees()->whereNotIn('id', \plunner\Planner::all()->pluck('id'))->firstOrFail();
        return [$group, $employee];
    }

    public function testDeleteNonExistingMeeting()
    {
        $non_existing_meeting_id = $this->getNonExistingMeetingId();

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $non_existing_meeting_id);
        $response->seeStatusCode(404);
    }

    public function testDeleteOtherGroupsMeeting()
    {
        $other_group = \plunner\Group::where('planner_id', '<>', $this->planner->id)->first();
        $other_groups_meeting_id = $other_group->meetings()->first()->id;

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/' . $other_group->id . '/meetings/' . $other_groups_meeting_id);
        $response->seeStatusCode(403);
    }

    public function testUpdateExistingMeeting()
    {
        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/' . $this->group->id . '/meetings', $this->data);
        $meeting = $this->group->meetings()->first();

        $test_data = $this->getUpdateData();

        $response = $this->actingAs($this->planner)
            ->json('PUT', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $meeting->id, $test_data);
        $response->assertResponseOk();
        $response->seeJson($test_data);
    }

    private function getUpdateData()
    {
        return [
            'title' => 'Different title',
            'description' => 'Different description!',
            'duration' => 60
        ];
    }

    public function testEmployeeUpdateExistingMeeting()
    {
        list($test_group, $test_employee) = $this->getNonPlannerInAGroup();

        $meeting = $test_group->meetings()->first();

        $test_data = $this->getUpdateData();

        $response = $this->actingAs($test_employee)
            ->json('PUT', 'employees/planners/groups/' . $test_group->id . '/meetings/' . $meeting->id, $test_data);
        $response->seeStatusCode(404);
    }

    public function testUpdateNonExistingMeeting()
    {
        $non_existing_meeting_id = $this->getNonExistingMeetingId();

        $test_data = $this->getUpdateData();

        $response = $this->actingAs($this->planner)
            ->json('PUT', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $non_existing_meeting_id, $test_data);
        $response->seeStatusCode(404);
    }

    public function testUpdateOtherGroupsMeeting()
    {
        $other_group = \plunner\Group::where('planner_id', '<>', $this->planner->id)->first();
        $other_groups_meeting_id = $other_group->meetings()->first()->id;

        $test_data = $this->getUpdateData();

        $response = $this->actingAs($this->planner)
            ->json('PUT', 'employees/planners/groups/' . $other_group->id . '/meetings/' . $other_groups_meeting_id, $test_data);
        $response->seeStatusCode(403);
    }
}
