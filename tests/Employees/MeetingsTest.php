<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MeetingsTest extends TestCase
{
    use DatabaseTransactions, ActingAs;

    private $group;

    public function setUp()
    {
        parent::setUp();

        $company = $this->makeCompany();
        $employees = $this->makeEmployees($company);
        $this->group = $this->makeGroup($company, $employees);
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
    }

    public function testCreateNonRepeatingMeeting()
    {
        $this->assertTrue(true);
    }

    public function testCreateRepeatingMeeting()
    {
        $this->assertTrue(true);
    }

    public function testShowNonRepeatingMeeting()
    {
        $this->assertTrue(true);
    }

    public function testShowRepeatingMeeting()
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
    }

    public function testUpdateMeeting()
    {
        $this->assertTrue(true);
    }
}
