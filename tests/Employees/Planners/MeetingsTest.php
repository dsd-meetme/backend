<?php

namespace Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class MeetingsTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company;
    private $group;
    private $planner;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = $this->makeCompany();
        $employees = $this->makeEmployees($this->company);
        $this->group = $this->makeGroup($this->company, $employees);

        $this->planner = $this->group->planner;
    }

    private function makeCompany()
    {
        return factory(\plunner\Company::class, 1)->create();
    }

    private function makeEmployees($company)
    {
        factory(\plunner\Employee::class, 3)->create(
            [
                'company_id' => $company->id,
            ]
        );

        return $company->employees;
    }

    private function makeGroup($company, $employees)
    {
        $group = factory(\plunner\Group::class, 1)->create(
            [
                'company_id' => $company->id,
                'planner_id' => $employees[0]->id,
            ]
        );
        $group->employees()->save($employees[0]);
        $group->employees()->save($employees[1]);
        $group->employees()->save($employees[2]);

        return $group;
    }


    public function testCreateNonRepeatingMeeting()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '0',
            'repetition_end_time' => '02.01.2016',
            'group_id' => $this->group->id,
        ];

        $response = $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data);

        $response->assertResponseOk();
        $response->seeJson($data);
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
            'repetition_end_time' => '02.01.2016',
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
