<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function testNewUser()
    {
        $response = $this->post('/auth/register', ['name'=>'test', 'email'=>'test@test.com', 'password_confirmation'=>'testtest', 'password'=>'testtest'], ['Accept'=>'application/json']);
        $response->seeStatusCode(302);
        $this->seeInDatabase("users", ['name'=>'test', 'email'=>'test@test.com']);

        //already logged (redirect to main page)
        $response = $this->post('/auth/register', [], ['Accept'=>'application/json']);
        $response->seeStatusCode(302);
    }

    public function testErrorNewUser()
    {
        $response = $this->post('/auth/register', ['name'=>'test', 'email'=>'test@test.com', 'password_confirmation'=>'atesttest', 'password'=>'testtest'], ['Accept'=>'application/json']);
        $response->seeStatusCode(422);
        $this->dontSeeInDatabase("users", ['name'=>'test', 'email'=>'test@test.com']);

        //not already logged (no redirect to main page)
        $response = $this->post('/auth/register', [], ['Accept'=>'application/json']);
        $response->seeStatusCode(422);

    }
}
