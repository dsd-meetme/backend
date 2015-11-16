<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function testNewUser()
    {
        $response = $this->json('POST','/auth/register', ['name'=>'test', 'email'=>'test@test.com', 'password_confirmation'=>'testtest', 'password'=>'testtest']);
        $response->seeStatusCode(302);
        $this->seeInDatabase("users", ['name'=>'test', 'email'=>'test@test.com']);

        //already logged (redirect to main page)
        $response = $this->json('POST','/auth/register', []);
        $response->seeStatusCode(302);
    }

    public function testErrorNewUser()
    {
        $response = $this->json('POST','/auth/register', ['name'=>'test', 'email'=>'test@test.com', 'password_confirmation'=>'atesttest', 'password'=>'testtest']);
        $response->seeStatusCode(422);
        $this->dontSeeInDatabase("users", ['name'=>'test', 'email'=>'test@test.com']);

        //not already logged (no redirect to main page)
        $response = $this->json('POST','/auth/register', []);
        $response->seeStatusCode(422);

    }

    public function testLogin()
    {
        $response = $this->post('/auth/login', ['email'=>'testInit@test.com', 'password'=>'test'], ['Accept'=>'application/json']);
        $response->seeStatusCode(302);
        $this->assertEquals($this->baseUrl, $response->response->headers->get('location'));
    }

    public function testErrorLogin()
    {
        $response = $this->post('/auth/login', ['email'=>'testInit@test.com', 'password'=>'atest'], ['Accept'=>'application/json']);
        $response->seeStatusCode(302);
        $this->assertEquals($this->baseUrl.'/auth/login', $response->response->headers->get('location'));
    }
}
