<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class CalendarsControllerTest extends TestCase
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
        $response = $this->actingAs($employee)->json('GET', '/employees/calendars');
        $response->assertResponseOk();
        $response->seeJsonEquals($employee->calendars()->with('caldav')->get()->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/employees/calendars');
        $response->seeStatusCode(401);
    }

    public function testCreate()
    {
        /**
         * @var $employee \plunner\Employee
         */
        $employee = \plunner\Employee::findOrFail(1);
        $data = [
            'name' => 'test',
            'enabled' => '1',
        ];

        //correct request
        $response = $this->actingAs($employee)->json('POST', '/employees/calendars/',$data);
        $response->assertResponseOk();
        $response->seeJson($data);

        //error request
        $response = $this->actingAs($employee)->json('POST', '/employees/calendars/',[]);
        $response->seeStatusCode(422);

        //force field
        $data['employee_id'] = 2;
        $response = $this->actingAs($employee)->json('POST', '/employees/calendars/',$data);
        $response->assertResponseOk();
        $json = $response->response->content();
        $json = json_decode($json, true);
        $this->assertNotEquals($data['employee_id'], $json['employee_id']); //this for travis problem due to consider 1 as number instead of string
        $this->assertEquals(1, $json['employee_id']);
        unset($data['employee_id']);
        $response->SeeJson($data);
    }

    public function testDelete()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->calendars()->with('caldav')->firstOrFail();
        $id = $calendar->id;

        //calendar exists
        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/'.$id);
        $response->assertResponseOk();
        $response->seeJsonEquals($calendar->toArray());

        //remove
        $response = $this->actingAs($employee)->json('DELETE', '/employees/calendars/'.$id);
        $response->assertResponseOk();

        //calendar doesn't exist
        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/'.$id);
        $response->seeStatusCode(404);

        //I cannot remove a removed calendar
        $response = $this->actingAs($employee)->json('DELETE', '/employees/calendars/'.$id);
        $response->seeStatusCode(404);
    }

    public function testUpdateNoCaldav()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->Calendars()->has('caldav','=','0')->firstOrFail();//TODO fix thsi with the new seeds
        $data = [
            'name' => 'test',
            'enabled' => '1',
        ];

        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/calendars/'.$calendar->id,$data);
        $response->assertResponseOk();
        $response->seeJson($data);


        //same calendar update
        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/calendars/'.$calendar->id,$data);
        $response->assertResponseOk();
        $response->seeJson($data);
    }

    public function testShow()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = $employee->Calendars()->with('caldav')->firstOrFail();

        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/'.$calendar->id);
        $response->assertResponseOk();
        $response->seeJsonEquals($calendar->toArray());
    }

    public function testShowNotMine()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = \plunner\Calendar::where('employee_id','<>', $employee->id)->firstOrFail();

        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/'.$calendar->id);
        $response->seeStatusCode(403);
    }

    public function testCreateCaldav()
    {
        /**
         * @var $employee \plunner\Employee
         */
        $employee = \plunner\Employee::findOrFail(1);
        $data = [
            'name' => 'test',
            'enabled' => '1',
            'url' => 'http://test.com',
            'username' => 'test',
            'password' => 'test',
            'calendar_name' => 'test',
        ];

        //correct request
        $response = $this->actingAs($employee)->json('POST', '/employees/calendars/caldav',$data);
        $response->assertResponseOk();
        $response->seeJson([
            'name' => 'test',
            'enabled' => '1',
        ]);

        //error request NO PASSWORD
        unset($data['password']);
        $response = $this->actingAs($employee)->json('POST', '/employees/calendars/caldav',$data);
        $response->seeStatusCode(422);
    }

    public function testUpdateCaldav()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $calendar = factory(\plunner\Calendar::class)->make(['enabled'=>true]);
        $employee->calendars()->save($calendar);
        $caldav = factory(\plunner\Caldav::class)->make();
        $calendar->caldav()->save($caldav);

        $data = [
            'name' => 'test',
            'enabled' => '1',
            'url' => 'http://test.com',
            'username' => 'test',
            'password' => 'test',
            'calendar_name' => 'test',
        ];

        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/calendars/'.$calendar->id,$data);
        $response->assertResponseOk();
        $response->seeJson([
            'name' => 'test',
            'enabled' => '1',
        ]);


        //same calendar update
        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/calendars/'.$calendar->id,$data);
        $response->assertResponseOk();
        $response->seeJson([
            'name' => 'test',
            'enabled' => '1',
        ]);

        //check caldav equals
        $caldav = $calendar->caldav->toArray();
        unset($caldav['updated_at']);
        unset($caldav['created_at']);
        unset($caldav['sync_errors']);
        unset($caldav['calendar_id']);
        $this->assertEquals([
            'url' => 'http://test.com',
            'username' => 'test',
            'calendar_name' => 'test',
        ],$caldav);
    }

    public function testGetExternalCalendars()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $data = [
            'url' => 'http://test.com',
            'username' => 'test',
            'password' => 'test',
        ];

        //error request
        $response = $this->actingAs($employee)->json('POST', '/employees/calendars/calendars',$data);
        $response->seeStatusCode(422);
        $response->seeJson(['error'=>"Invalid URL: 'http://test.com'"]);
    }
}
