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
            if ($test_meeting_id == !$this->group->meetings->where("id", $test_meeting_id)->id)
            {
                break;
            }
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
        // Assure the employee is not the groups planner
        $test_group = $this->employee->groups->where($this->employee->id, '<>' , $this->planner->id)->first();
        $test_planner = $test_group->planner;

        $this->actingAs($test_planner)
            ->json('POST', 'employees/planners/groups/'.$test_group->id.'/meetings', $this->data);
        $meeting_id = $test_group->meetings()->first()->id;

        $response = $this->actingAs($this->employee)
            ->json('DELETE', 'employees/planners/groups/'.$test_group->id.'/meetings/'.$meeting_id);
        $response->seeStatusCode(403);
    }

    public function testDeleteNonExistingMeeting()
    {
        $test_meeting_id = 0;

        // Find an id of a non existing meeting
        for ($test_meeting_id; $test_meeting_id < $this->group->meetings->count() + 1; $test_meeting_id++)
        {
            if ($test_meeting_id == !$this->group->meetings->where("id", $test_meeting_id)->id)
            {
                break;
            }
        }

        $response = $this->actingAs($this->planner)
            ->json('DELETE', 'employees/planners/groups/'.$this->group->id.'/meetings/'.$test_meeting_id);
        $response->seeStatusCode(404);
    }

    public function testUpdateExistingMeeting()
    {
        $meeting_id = $this->group->meetings()->first()->id;
        $test_data = $this->data;
        $test_data->title = "Different title";
        $test_data->description = "Different description";

        $response = $this->actingAs($this->planner)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings'.$meeting_id, $test_data);
        $response->assertResponseOk();
        $response->seeJson($test_data);
    }

    public function testEmployeeUpdateExistingMeeting()
    {
        // Assure the employee is not the groups planner
        $test_group = $this->employee->groups->where($this->employee->id, '<>' , $this->planner->id)->first();
        $test_planner = $test_group->planner;

        $this->actingAs($test_planner)
            ->json('POST', 'employees/planners/groups/'.$test_group->id.'/meetings', $this->data);
        $meeting_id = $this->group->meetings()->first()->id;

        $test_data = $this->data;
        $test_data->title = "Different title";
        $test_data->description = "Different description";

        $response = $this->actingAs($this->employee)
            ->json('POST', 'employees/planners/groups/'.$this->group->id.'/meetings'.$meeting_id, $test_data);
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
