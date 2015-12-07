<?php

namespace Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class MeetingsTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $group, $employee, $planner, $data_non_repeat, $data_repeat;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = \plunner\Company::findOrFail(1);
        $this->employee = $this->company->employees()->with('groups')->last();
        $this->group = $this->employee->groups->first();
        $this->planner = $this->group->planner;

        $this->data_non_repeat= [
            'title' => 'Test non-repeating meeting',
            'description' => 'Errare humanum est!',
            'start_time' => '2015-12-07 12:00:00',
            'end_time' => '2015-12-07 14:00:00',
            'repeat' => '0',
            'is_scheduled' => false,
            'group_id' => $this->group->id,
        ];
    }


    public function testCreateNonRepeatingMeeting()
    {
        $response = $this->actingAs($this->planner)->json('POST', '/employees/meetings', $this->data_non_repeat);

        $response->assertResponseOk();
        $response->seeJson($this->data_non_repeat);
    }

    public function testCreateDuplicateNonRepeatingMeeting()
    {
        $this->actingAs($this->planner)->json('POST', '/employees/meetings', $this->data_non_repeat);
        $response = $this->actingAs($this->planner)->json('POST', '/employees/meetings', $this->data_non_repeat);

        $response->seeStatusCode(422);
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

    public function testShowNonRepeatingMeeting()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '0',
            'group_id' => $this->group->id,
        ];
        $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data);

        $meeting_id = $this->company->employees()->first()->meetings()->first()->id;
        $response = $this->actingAs($this->planner)->json('GET', '/employees/meetings/' . $meeting_id);

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

    public function testDeleteMeeting()
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
        $response = $this->actingAs($this->planner)->json('DELETE', '/employees/meetings/' . $meeting_id);

        $response->assertResponseOk();
    }

    public function testDeleteNonExistingMeeting()
    {
        $meeting_id = $this->company->employees()->first()->meetings()->first()->id;
        $response = $this->actingAs($this->planner)->json('DELETE', '/employees/meetings/' . $meeting_id);

        $response->seeStatusCode(404);
    }*/

//    TODO tests for : update, duplicate title within same group
}
