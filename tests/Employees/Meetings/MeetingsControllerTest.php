<?php

namespace Employees\Meetings;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class MeetingsControllerTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $employee, $meeting;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);

        $this->company = factory(\plunner\Company::class)->create();
        $this->employee = factory(\plunner\Employee::class)->make();
        $this->company->employees()->save($this->employee);
        $group = factory(\plunner\Group::class)->make();
        $this->company->groups()->save($group);
        $this->employee->groups()->attach($group);
        $this->meeting = factory(\plunner\Meeting::class)->make();
        $group->meetings()->save($this->meeting);
        $this->meeting->start_time = new \DateTime();
        $this->meeting->save();
        $this->employee->meetings()->attach($this->meeting->id);
    }


    public function testIndex()
    {
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/meetings');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->employee->meetings()->get()->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/employees/meetings');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $meeting_id = $this->employee->meetings->first()->id;
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/meetings/'.$meeting_id);

        $response->assertResponseOk();
        $response->seeJsonEquals($this->employee->meetings()->first()->toArray());
    }

    public function testShowGroupNotInSameCompany()
    {
        $test_company = \plunner\Company::where('id', '<>', $this->company->id)->firstOrFail();
        $test_group = $test_company->groups()->has('meetings')->firstOrFail();
        $test_meeting = $test_group->meetings()->firstOrFail();
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/meetings/'.$test_meeting->id);
        $response->seeStatusCode(403);
    }
}
