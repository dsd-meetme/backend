<?php

namespace Companies\Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use plunner\Company;
use plunner\Planner;
use Tymon\JWTAuth\Support\testing\ActingAs;

class MeetingTimeslotsTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $group, $employee, $planner, $meeting, $timeslot, $data;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => Planner::class]);
        config(['jwt.user' => Planner::class]);

        $this->company = Company::findOrFail(1);
        $this->employee = $this->company->employees()->with('groups')->first();
        $this->group = $this->employee->groups->first();
        $this->planner = $this->group->planner;
        $this->meeting = $this->group->meetings()->has('timeslots')->with('group')->first();

        $this->data = [
            'time_start' => '2015-12-17 12:00:00',
            'time_end' => '2015-12-17 14:00:00',
        ];

        $this->meeting->timeslots()->create($this->data);

        $this->timeslot = $this->meeting->timeslots()->with('meeting')->firstOrFail();
    }

    public function testIndex()
    {
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $this->meeting->id . '/timeslots');
        $response->assertResponseOk();
        $response->seeJsonEquals($this->meeting->timeslots->toArray());
    }

    public function testEmployeeViewIndex()
    {
        list($test_group, $test_employee) = $this->getNonPlannerInAGroup();

        $meeting = $test_group->meetings()->firstOrFail();

        $response = $this->actingAs($test_employee)
            ->json('GET', 'employees/planners/groups/' . $test_group->id . '/meetings/' . $meeting->id . '/timeslots');
        $response->seeStatusCode(404);
    }

    private function getNonPlannerInAGroup()
    {
        $group = \plunner\Group::has('employees', '>=', '2')->has('meetings', '>=', '1')
            ->whereHas('employees', function ($query) {
                $query->whereNotIn('id', \plunner\Planner::all()->pluck('id')); //TODO do in a better way less expensive
            })->firstOrFail();
        $employee = $group->employees()->whereNotIn('id', \plunner\Planner::all()->pluck('id'))->firstOrFail();
        return [$group, $employee];
    }

    public function testShow()
    {
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $this->meeting->id . '/timeslots/' . $this->timeslot->id);
        $response->assertResponseOk();
        $response->seeJsonEquals($this->timeslot->toArray());
    }

    public function testEmployeeViewShow()
    {
        list($test_group, $test_employee) = $this->getNonPlannerInAGroup();

        $meeting = $test_group->meetings()->firstOrFail();
        $meeting_timeslot = $meeting->timeslots()->firstOrFail();
        $response = $this->actingAs($test_employee)
            ->json('GET', 'employees/planners/groups/' . $test_group->id . '/meetings/' . $meeting->id . '/timeslots/' . $meeting_timeslot->id);
        $response->seeStatusCode(404);
    }

    public function testIfStatementsFail()
    {
        $group = factory(\plunner\Group::class)->make();
        $this->company->groups()->save($group);
        $meeting = factory(\plunner\Meeting::class)->make();
        $group->meetings()->save($meeting);
        $timeslot = factory(\plunner\MeetingTimeslot::class)->make();
        $meeting->timeslots()->save($timeslot);
        $group->planner_id = $this->planner->id;
        $group->save();

        //index
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $group->id . '/meetings/' . $this->meeting->id . '/timeslots/');
        $response->seeStatusCode(403);
        //get
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $group->id . '/meetings/' . $this->meeting->id . '/timeslots/' . $this->timeslot->id);
        $response->seeStatusCode(403);
        //post
        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/' . $group->id . '/meetings/' . $this->meeting->id . '/timeslots/', $this->data);
        $response->seeStatusCode(403);
        //update
        $response = $this->actingAs($this->planner)
            ->json('PUT', 'employees/planners/groups/' . $group->id . '/meetings/' . $this->meeting->id . '/timeslots/' . $this->timeslot->id, $this->data);
        $response->seeStatusCode(403);
        //delete
        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/' . $group->id . '/meetings/' . $this->meeting->id . '/timeslots/' . $this->timeslot->id);
        $response->seeStatusCode(403);
    }

    public function testCreate()
    {
        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $this->meeting->id . '/timeslots',
                $this->data);
        $response->assertResponseOk();
        $response->seeJson($this->data);
    }

    public function testPlannedCreate()
    {
        $meeting = factory(\plunner\Meeting::class)->make(['start_time' => new \DateTime()]);
        $this->group->meetings()->save($meeting);
        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $meeting->id . '/timeslots',
                $this->data);
        $response->seeStatusCode(422);
    }

    public function testEmployeeCreate()
    {
        list($test_group, $test_employee) = $this->getNonPlannerInAGroup();

        $meeting = $test_group->meetings()->firstOrFail();

        $response = $this->actingAs($test_employee)
            ->json('POST', 'employees/planners/groups/' . $test_group->id . '/meetings/' . $meeting->id . '/timeslots',
                $this->data);
        $response->seeStatusCode(404);
    }

    public function testUpdate()
    {
        $test_data = [
            'time_start' => '2015-12-17 14:00:00',
            'time_end' => '2015-12-17 15:00:00',
        ];

        $response = $this->actingAs($this->planner)
            ->json('PUT', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $this->meeting->id . '/timeslots/' . $this->timeslot->id, $test_data);
        $response->assertResponseOk();
        $response->seeJson($test_data);

        $this->timeslot = $this->timeslot->fresh();
        $this->assertEquals($test_data['time_start'], $this->timeslot->time_start);
        $this->assertEquals($test_data['time_end'], $this->timeslot->time_end);
    }

    public function testPlannedUpdate()
    {
        $meeting = factory(\plunner\Meeting::class)->make(['start_time' => new \DateTime()]);
        $this->group->meetings()->save($meeting);
        $timeslot = factory(\plunner\MeetingTimeslot::class)->make();
        $meeting->timeslots()->save($timeslot);
        $test_data = [
            'time_start' => '2015-12-17 14:00:00',
            'time_end' => '2015-12-17 15:00:00',
        ];

        $response = $this->actingAs($this->planner)
            ->json('PUT', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $meeting->id . '/timeslots/' . $timeslot->id, $test_data);
        $response->seeStatusCode(422);
    }

    public function testEmployeeUpdate()
    {
        list($test_group, $test_employee) = $this->getNonPlannerInAGroup();

        $meeting = $test_group->meetings()->firstOrFail();
        $meeting_timeslot = $meeting->timeslots()->firstOrFail();

        $test_data = [
            'time_start' => '2015-12-17 14:00:00',
            'time_end' => '2015-12-17 15:00:00',
        ];

        $response = $this->actingAs($test_employee)
            ->json('PUT', 'employees/planners/groups/' . $test_group->id . '/meetings/' . $meeting->id . '/timeslots/' . $meeting_timeslot->id, $test_data);
        $response->seeStatusCode(404);
    }

    public function testDestroy()
    {
        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $this->meeting->id . '/timeslots/' . $this->timeslot->id);
        $response->assertResponseOk();

        // GET unable to get deleted

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $this->meeting->id . '/timeslots/' . $this->timeslot->id);
        $response->seeStatusCode(404);

        // DESTROY non existing

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $this->meeting->id . '/timeslots/' . $this->timeslot->id);
        $response->seeStatusCode(404);

    }

    public function testPlanedDestroy()
    {
        $meeting = factory(\plunner\Meeting::class)->make(['start_time' => new \DateTime()]);
        $this->group->meetings()->save($meeting);
        $timeslot = factory(\plunner\MeetingTimeslot::class)->make();
        $meeting->timeslots()->save($timeslot);

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $meeting->id . '/timeslots/' . $timeslot->id);
        $response->seeStatusCode(422);

        // GET OK
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/' . $this->group->id . '/meetings/' . $meeting->id . '/timeslots/' . $timeslot->id);
        $response->assertResponseOk();
    }

    public function testEmployeeDestroy()
    {
        list($test_group, $test_employee) = $this->getNonPlannerInAGroup();

        $meeting = $test_group->meetings()->firstOrFail();
        $meeting_timeslot = $meeting->timeslots()->firstOrFail();

        $response = $this->actingAs($test_employee)
            ->json('DELETE', 'employees/planners/groups/' . $test_group->id . '/meetings/' . $meeting->id . '/timeslots/' . $meeting_timeslot->id);
        $response->seeStatusCode(404);
    }

}
