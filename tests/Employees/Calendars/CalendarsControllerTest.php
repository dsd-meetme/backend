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
        $response->response->content();
        $response->seeJsonEquals($employee->calendars->toArray());
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

        //duplicate employee
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
        $id = $employee->id;

        //calendar exists
        $response = $this->actingAs($employee)->json('GET', '/employees/calendars/'.$id);
        $response->assertResponseOk();
        $response->seeJsonEquals($employee->calendars->toArray());

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

    public function testUpdate()
    {
        $employee = \plunner\Employee::findOrFail(1);
        $data = [
            'name' => 'test',
            'enabled' => '1',
        ];

        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/calendars/'.$employee->id,$data);
        $response->assertResponseOk();
        $response->seeJson($data);


        //same calendar update
        //correct request
        $response = $this->actingAs($employee)->json('PUT', '/employees/calendars/'.$employee->id,$data);
        $response->assertResponseOk();
        $response->seeJson($data);
    }
}
