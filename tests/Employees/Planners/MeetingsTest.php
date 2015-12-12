<?php

namespace Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class PlannersMeetingsTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $group, $employee, $planner, $data, $data_repeat;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->employee = $this->company->employees()->with('groups')->first();
        $this->group = $this->employee->groups->first();
        $this->planner = $this->group->planner;

        $this->data= [
            'title' => 'Test non-repeating meeting',
            'description' => 'Errare humanum est!',
            'duration' => 120
        ];
    }

    public function testCreateNonRepeatingMeeting()
    {
        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings', $this->data);

        $response->assertResponseOk();
        $response->seeJson($this->data);
    }

    public function testCreateDuplicateNonRepeatingMeeting()
    {
        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings', $this->data);

        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings', $this->data);
        $response->seeStatusCode(422);
    }

    public function testIndexAllMeetings()
    {
        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->group->meetings->toArray());
    }

    public function testErrorIndexNoMeetings()
    {
        $response = $this->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings');

        $response->seeStatusCode(401);
    }

    public function testShowNonRepeatingMeeting()
    {
        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings', $this->data);
        $meeting_id = $this->group->meetings->first()->id;

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings'.$meeting_id);
        $response->assertResponseOk();
        $response->seeJsonEquals($this->data->toArray());
    }

    public function testShowNonExistingMeeting()
    {
        $test_meeting_id = 0;

        // Find an id of a non existing meeting
        for ($test_meeting_id; $test_meeting_id < $this->group->meetings->count() + 1; $test_meeting_id++)
        {
            $meeting = $this->group->meetings->where("id", $test_meeting_id);
            if (is_null($meeting))
                // If $meeting is null that means the $test_meeting_id is an id of non-existing meeting and we can break
                break;
        }

        $response = $this->actingAs($this->planner)
            ->json('GET', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$test_meeting_id);
        $response->seeStatusCode(404);
    }

    public function testPlannerDeleteMeeting()
    {
        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings', $this->data);
        $meeting_id = $this->group->meetings()->first()->id;

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$meeting_id);
        $response->assertResponseOk();
    }

    public function testEmployeeDeleteMeeting()
    {
        // Find an employee in the group who is not a planner and set him as $test_employee
        if ($this->employee->id == $this->planner->id) {
            foreach ($this->group->employees as $employee) {
                if ($this->employee->id != $employee->id)
                    $test_employee = $employee;
                    break;
            }
        }
        else { $test_employee = $this->employee; }

        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings', $this->data);
        $meeting_id = $this->group->meetings()->first()->id;

        $response = $this->actingAs($test_employee)
            ->json('DELETE', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$meeting_id);
        $response->seeStatusCode(403);
    }

    public function testDeleteNonExistingMeeting()
    {
        $test_meeting_id = 0;

        // Find an id of a non existing meeting
        for ($test_meeting_id; $test_meeting_id < $this->group->meetings->count() + 1; $test_meeting_id++)
        {
            $meeting = $this->group->meetings->where("id", $test_meeting_id);
            if (is_null($meeting))
                // If $meeting is null that means the $test_meeting_id is an id of non-existing meeting and we can break
                break;
        }

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$test_meeting_id);
        $response->seeStatusCode(404);
    }

    public function testUpdateExistingMeeting()
    {
        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings', $this->data);
        $meeting = $this->group->meetings()->first();

        $test_data = [
            'title' => 'Different title',
            'description' => 'Different description!',
            'duration' => 60
        ];

        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings'.$meeting->id, $test_data);
        $response->assertResponseOk();
        $response->seeJson($test_data);
    }

    public function testEmployeeUpdateExistingMeeting()
    {
        // Find an employee in the group who is not a planner and set him as $test_employee
        if ($this->employee->id == $this->planner->id) {
            foreach ($this->group->employees as $employee) {
                if ($this->employee->id != $employee->id)
                    $test_employee = $employee;
                break;
            }
        }
        else { $test_employee = $this->employee; }

        $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings', $this->data);
        $meeting = $this->group->meetings()->first();

        $test_data = [
            'title' => 'Different title',
            'description' => 'Different description!',
            'duration' => 60
        ];

        $response = $this->actingAs($test_employee)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings'.$meeting->id, $test_data);
        $response->seeStatusCode(403);
    }

    /*public function testCreateRepeatingMeeting()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '10',
            'repetition_end_time' => '02.05.2016',
            'group_id' => $this->group->id,
        ];

        $response = $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data);

        $response->assertResponseOk();
        $response->seeJson($data);
    }

    public function testShowRepeatingMeeting()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '10',
            'repetition_end_time' => '02.05.2016',
            'group_id' => $this->group->id,
        ];
        $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data);

        $meeting_id = $this->company->employees()->first()->meetings()->first()->id;
        $response = $this->actingAs($this->planner)->json('GET', '/employees/meetings/' . $meeting_id);

        $response->assertResponseOk();
        $response->seeJson($data);
    }

    public function testShowAllMeetingsInMonth()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '10',
            'repetition_end_time' => '02.05.2016',
            'group_id' => $this->group->id,
        ];
        $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data);

        $meeting_id = $this->company->employees()->first()->meetings()->first()->id;
        $response = $this->actingAs($this->planner)->json('GET', '/employees/meetings/' . $meeting_id);

        $response->assertResponseOk();
        $response->seeJson($data);
    }

    */
}
