<?php

namespace Employees\Meetings;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class MeetingsControllerTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    private $company, $employee, $meeting, $group;

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
        $this->group = $this->employee->groups()->firstOrFail();
    }


    public function testIndex()
    {
        $new = factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->add(new \DateInterval('PT100S'))]);
        $this->group->meetings()->save($new); // new planed
        $this->employee->meetings()->attach($new->id);
        $old = factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->sub(new \DateInterval('PT100S'))]);
        $this->group->meetings()->save($old); // old planed
        $this->employee->meetings()->attach($old->id);
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/meetings');

        $response->assertResponseOk();
        $response->seeJsonEquals($this->employee->meetings()->get()->toArray());
    }

    public function testIndexCurrent()
    {
        //one meeting planed new, one meeting planed old, one to be planed
        $group = factory(\plunner\Group::class)->make();
        $this->company->groups()->save($group);
        $this->employee->groups()->attach($group);
        $group->meetings()->save(factory(\plunner\Meeting::class)->make()); //to be planed
        $new = factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->add(new \DateInterval('PT100S'))]);
        $group->meetings()->save($new); // new planed
        $this->employee->meetings()->attach($new->id);
        $old = factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->sub(new \DateInterval('PT100S'))]);
        $group->meetings()->save($old); // old planed
        $this->employee->meetings()->attach($old->id);

        //other planner meeting planned to test or condition
        $groupOther = \plunner\Group::whereNotIn('id', $this->employee->groups->pluck('id'))->firstOrFail();
        $other = factory(\plunner\Meeting::class)->make(['start_time' => (new \DateTime())->add(new \DateInterval('PT100S'))]);
        $groupOther->meetings()->save($other);
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/meetings/?current=1');

        $response->assertResponseOk();
        $this->employee = $this->employee->fresh();
        $response->seeJsonEquals($this->employee->meetings()->where(function ($query) {
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

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/employees/meetings');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $meeting_id = $this->employee->meetings->first()->id;
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/meetings/' . $meeting_id);

        $response->assertResponseOk();
        $response->seeJsonEquals($this->employee->meetings()->first()->toArray());
    }

    public function testShowGroupNotInSameCompany()
    {
        $test_company = \plunner\Company::where('id', '<>', $this->company->id)->firstOrFail();
        $test_group = $test_company->groups()->has('meetings')->firstOrFail();
        $test_meeting = $test_group->meetings()->firstOrFail();
        $response = $this->actingAs($this->employee)
            ->json('GET', '/employees/meetings/' . $test_meeting->id);
        $response->seeStatusCode(403);
    }
}
