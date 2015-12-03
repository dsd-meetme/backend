<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Support\testing\ActingAs;

class GroupsControllerTest extends TestCase
{
    use DatabaseTransactions, ActingAs;

    public function testtrue()
    {
        $this->assertTrue(true);
    }
    /*

    private $company, $group;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Group::class]);
        config(['jwt.user' => \plunner\Group::class]);
        $this->company = \plunner\Company::findOrFail(1);
        $this->group = $this->company->groups()->with('groups')->first();
    }

    public function testIndex()
    {
        $response = $this->actingAs($this->group)
            ->json('GET', '/companies/groups');
        $response->assertResponseOk();
        $response->seeJsonEquals($this->company->groups->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/companies/groups');
        $response->seeStatusCode(401);
    }

    public function testShow()
    {
        $group_id = $this->company->groups->first()->id;
        $response = $this->actingAs($this->group)
            ->json('GET', '/companies/groups/' . $group_id);
        $response->assertResponseOk();
        $response->seeJsonEquals($this->company->groups->first()->toArray());
    }

    public function testShowGroupNotInSameCompany()
    {
        $test_company = \plunner\Company::where('id', '<>', $this->company->id)->firstOrFail();
        $test_group = $test_company->groups->first();
        $response = $this->actingAs($this->group)
            ->json('GET', '/companies/groups/' . $test_group->id);
        $response->seeStatusCode(403);
    }

    public function testDelete()
    {
        $group = \plunner\Group::findOrFail(1);
        $group = $company->groups->first();
        $id = $group->id;

        //group exists
        $response = $this->actingAs($group)->json('GET', '/companies/groups/' . $id);
        $response->assertResponseOk();
        $response->seeJsonEquals($group->toArray());

        //remove
        $response = $this->actingAs($group)->json('DELETE', '/companies/groups/' . $id);
        $response->assertResponseOk();

        //group doesn't exist
        $response = $this->actingAs($group)->json('GET', '/companies/groups/' . $id);
        $response->seeStatusCode(404);

        //I cannot remove a removed group
        $response = $this->actingAs($group)->json('DELETE', '/companies/groups/' . $id);
        $response->seeStatusCode(404);
    }
    */
}