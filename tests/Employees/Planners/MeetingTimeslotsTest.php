<?php

namespace Companies\Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use plunner\Company;
use plunner\Employee;
use plunner\Group;
use plunner\Meeting;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Support\testing\ActingAs;

class MeetingTimeslotsTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $group, $employee, $planner, $meeting, $meeting_timeslot, $data;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => Employee::class]);
        config(['jwt.user' => Employee::class]);

        $this->company = Company::findOrFail(1);
        $this->employee = $this->company->employees()->with('groups')->first();
        $this->group = $this->employee->groups->first();
        $this->planner = $this->group->planner;
        $this->meeting = $this->group->meetings()->with('meeting_timeslots')->first();
        $this->meeting_timeslot = $this->meeting->timeslots->first();

        $this->data= [
            'time_start' => '2015-12-17 12:00:00',
            'time_end' => '2015-12-17 14:00:00',
        ];
    }

    public function testIndex()
    {
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots');
        $response->assertResponseOk();
        $response->seeJsonEquals($this->meeting->timeslots->toArray());
    }

    public function testEmployeeViewIndex()
    {
        $response = $this->actingAs($this->employee)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots');
        $response->seeStatusCode(403);
    }

    public function testIndexIfStatementFail()
    {
        $test_group = Group::where('id', '<>', $this->group->id)->firstOrFail();

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$test_group->id.'/meetings/'.$this->meeting->id.'/timeslots');
        $response->seeStatusCode(404);
    }

    public function testErrorIndex()
    {
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id);
        $response->assertResponseOk();
        $response->seeJsonEquals($this->meeting_timeslot->toArray());
    }

    public function testEmployeeViewShow()
    {
        $response = $this->actingAs($this->employee)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id);
        $response->seeStatusCode(403);
    }

    public function testShowIfStatementFail()
    {
        $test_group = Group::where('id', '<>', $this->group->id)->firstOrFail();

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$test_group->id.'/meetings/'.$this->meeting->id.'/timeslots'
                .$this->meeting_timeslot->id);
        $response->seeStatusCode(404);


        $test_meeting = Meeting::where('id', '<>', $this->meeting->id)->firstOrFail();

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$test_meeting->id.'/timeslots'
                .$this->meeting_timeslot->id);
        $response->seeStatusCode(404);
    }

    public function testCreate()
    {
        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots',
                $this->data);
        $response->assertResponseOk();
        $response->seeJson($this->data);
    }

    public function testEmployeeCreate()
    {
        $response = $this->actingAs($this->employee)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots',
                $this->data);
        $response->seeStatusCode(403);
    }

    public function testCreateIfStatementFail()
    {
        $test_group = Group::where('id', '<>', $this->group->id)->firstOrFail();

        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$test_group->id.'/meetings/'.$this->meeting->id.'/timeslots',
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
            ->json('PUT', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id, $test_data);
        $response->assertResponseOk();
        $response->seeJson($test_data);

        $this->assertEquals($test_data['time_start'], $this->meeting_timeslot->time_start);
        $this->assertEquals($test_data['time_end'], $this->meeting_timeslot->time_end);
    }

    public function testEmployeeUpdate()
    {
        $test_data = [
            'time_start' => '2015-12-17 14:00:00',
            'time_end' => '2015-12-17 15:00:00',
        ];
        $response = $this->actingAs($this->employee)
            ->json('PUT', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id, $test_data);
        $response->seeStatusCode(403);
    }

    public function testUpdateIfStatementFail()
    {
        $test_data = [
            'time_start' => '2015-12-17 14:00:00',
            'time_end' => '2015-12-17 15:00:00',
        ];

        $test_group = Group::where('id', '<>', $this->group->id)->firstOrFail();

        $response = $this->actingAs($this->planner)
            ->json('PUT', 'employees/planners/groups/'.$test_group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id, $test_data);
        $response->seeStatusCode(404);


        $test_meeting = Meeting::where('id', '<>', $this->meeting->id)->firstOrFail();

        $response = $this->actingAs($this->planner)
            ->json('PUT', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$test_meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id, $test_data);
        $response->seeStatusCode(404);
    }

    public function testDestroy()
    {
        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id);
        $response->assertResponseOk();

        // GET unable to get deleted

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id);
        $response->seeStatusCode(404);

        // DESTROY non existing

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id);
        $response->seeStatusCode(404);

    }

    public function testEmployeeDestroy()
    {
        $response = $this->actingAs($this->employee)
            ->json('DELETE', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id);
        $response->seeStatusCode(403);
    }

    public function testDestroyIfStatementFail()
    {
        $test_group = Group::where('id', '<>', $this->group->id)->firstOrFail();

        $response = $this->actingAs($this->planner)
            ->json('DESTROY', 'employees/planners/groups/'.$test_group->id.'/meetings/'.$this->meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id);
        $response->seeStatusCode(404);


        $test_meeting = Meeting::where('id', '<>', $this->meeting->id)->firstOrFail();

        $response = $this->actingAs($this->planner)
            ->json('DESTROY', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$test_meeting->id.'/timeslots/'
                .$this->meeting_timeslot->id);
        $response->seeStatusCode(404);
    }

}
