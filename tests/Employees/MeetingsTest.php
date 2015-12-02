<?php

namespace Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;
use plunner;

class MeetingsTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $planner;
    private $group;

    public function setUp()
    {
        parent::setUp();

        $company = $this->makeCompany();
        $employees = $this->makeEmployees($company);
        $this->group = $this->makeGroup($company, $employees);

        $this->planner = $this->group->planner;
    }

    private function makeCompany()
    {
        return factory(plunner\Company::class, 1)->create();
    }

    private function makeEmployees($company)
    {
        factory(plunner\Employee::class, 3)->create(
            [
                'company_id' => $company->id,
            ]
        );

        return $company->employees;
    }

    private function makeGroup($company, $employees)
    {
        $group = factory(plunner\Group::class, 1)->create(
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

    /*public function testCreateNonRepeatingMeeting()
    {
        $data = [
            'title' => 'Requirements meeting',
            'description' => 'Discussing the requirements',
            'start_time' => '20.12.2015',
            'end_time' => '02.01.2016',
            'repeat' => '0',
            'repetition_end_time' => '02.01.2016',
        ];

        $response = $this->actingAs($this->planner)->json('POST', '/employees/meetings', $data, $this->group->id);
        $response->assertResponseOk();
        $response->seeJson($data);
    }*/

    /*public function testCreateRepeatingMeeting()
    {
        $this->assertTrue(true);
    }

    public function testShowNonRepeatingMeeting()
    {
        $this->assertTrue(true);
    }*/

    /*public function testShowRepeatingMeeting()
    {
        $this->assertTrue(true);
    }

    public function testShowAllMeetingsInMonth()
    {
        $this->assertTrue(true);
    }

    public function testShowAllMeetingsInEmptyMonth()
    {
        $this->assertTrue(true);
    }

    public function testShowAllMeetingsInMonthWithOnlyRepeatingMeetings()
    {
        $this->assertTrue(true);
    }

    public function testDeleteMeeting()
    {
        $this->assertTrue(true);
    }*/

    public function testUpdateMeeting()
    {
        $this->assertTrue(true);
    }
}
