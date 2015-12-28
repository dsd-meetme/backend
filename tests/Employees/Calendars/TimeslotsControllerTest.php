<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class TimeslotsControllerTest extends TestCase
{
    use DatabaseTransactions, ActingAs;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Employee::class]);
        config(['jwt.user' => \plunner\Employee::class]);
    }


    public function testIndex()
    {
        /**
         * @var $employee \plunner\Employee
         */
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->calendars()->firstOrFail();
        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/' . $calendar->id . '/timeslots');
        $response->assertResponseOk();
        $response->seeJsonEquals($calendar->timeslots->toArray());
    }

    public function testErrorIndex()
    {
        /**
         * @var $employee \plunner\Employee
         */
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->calendars()->firstOrFail();
        $response = $this->json('GET', '/employees/calendars/' . $calendar->id . '/timeslots');
        $response->seeStatusCode(401);
    }

    public function testCreate()
    {
        /**
         * @var $employee \plunner\Employee
         */
        $employee = \plunner\Employee::findOrFail(1);
        $data = [
            'time_start' => '2015-12-17 12:00:00',
            'time_end' => '2015-12-17 14:00:00',
        ];
        $calendar = $employee->calendars()->firstOrFail();

        //correct request
        $response = $this->actingAs($employee)->json('POST', '/employees/calendars/' . $calendar->id . '/timeslots', $data);
        $response->assertResponseOk();
        $response->seeJson($data);
    }

    public function testDelete()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->calendars()->firstOrFail();
        $timeslot = $calendar->timeslots()->firstOrFail();

        //timeslot exists
        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/' . $calendar->id . '/timeslots/' . $timeslot->id);
        $response->assertResponseOk();
        $response->seeJsonEquals($timeslot->toArray());

        //remove
        $response = $this->actingAs($employee)->json('DELETE', '/employees/calendars/' . $calendar->id . '/timeslots/' . $timeslot->id);
        $response->assertResponseOk();

        //timeslot doesn't exist
        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/' . $calendar->id . '/timeslots/' . $timeslot->id);
        $response->seeStatusCode(404);

        //I cannot remove a removed timeslot
        $response = $this->actingAs($employee)->json('DELETE', '/employees/calendars/' . $calendar->id . '/timeslots/' . $timeslot->id);
        $response->seeStatusCode(404);
    }

    public function testUpdate()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->Calendars()->firstOrFail();
        $timeslot = $calendar->timeslots()->firstOrFail();
        $data = [
            'time_start' => '2015-12-17 10:00:00',
            'time_end' => '2015-12-17 17:00:00',
        ];

        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/calendars/' . $calendar->id . '/timeslots/' . $timeslot->id, $data);
        $response->assertResponseOk();
        $response->seeJson($data);


        //same timeslot update
        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/calendars/' . $calendar->id . '/timeslots/' . $timeslot->id, $data);
        $response->assertResponseOk();
        $response->seeJson($data);
    }


    public function testShow()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->Calendars()->firstOrFail();
        $timeslot = $calendar->timeslots()->firstOrFail();

        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/' . $calendar->id . '/timeslots/' . $timeslot->id);
        $response->assertResponseOk();
        $response->seeJsonEquals($timeslot->toArray());
    }

    public function testShowNotMine()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->Calendars()->firstOrFail();
        $timeslot = \plunner\Timeslot::whereNotIn('calendar_id', $employee->calendars()->get()->pluck('id'))->firstOrFail();

        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/' . $calendar->id . '/timeslots/' . $timeslot->id);
        $response->seeStatusCode(403);
    }
}
