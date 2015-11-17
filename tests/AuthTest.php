<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function testNewUser()
    {
        $response = $this->json('POST', '/auth/register', ['name' => 'test', 'email' => 'test@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest']);
        $response->seeStatusCode(200);
        $this->seeInDatabase("users", ['name' => 'test', 'email' => 'test@test.com']);

        //already logged (redirect to main page)
        $response = $this->json('POST', '/auth/register', []);
        $response->seeStatusCode(302);
    }

    public function testErrorNewUser()
    {
        $response = $this->json('POST', '/auth/register', ['name' => 'test', 'email' => 'test@test.com', 'password_confirmation' => 'atesttest', 'password' => 'testtest']);
        $response->seeStatusCode(422);
        $this->notSeeInDatabase("users", ['name' => 'test', 'email' => 'test@test.com']);

        //not already logged (no redirect to main page)
        $response = $this->json('POST', '/auth/register', []);
        $response->seeStatusCode(422);

    }

    public function testLogin()
    {
        $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $response->seeStatusCode(200);
    }

    public function testErrorLogin()
    {
        $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'atest']);
        $response->seeStatusCode(422);
    }

    public function testLogout()
    {
        $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $this->json('GET', '/auth/logout');
        $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'atest']);
        $response->seeStatusCode(422);
    }

    public function testResetPassword()
    {
        //perform restore request
        $response = $this->json('POST', '/password/email', ['email' => 'testInit@test.com']);
        $response->seeStatusCode(200);

        //get the token
        $token = DB::table('password_resets')->where('email', 'testInit@test.com')->value('token');

        //perform reset with error
        $response = $this->json('POST', '/password/reset', ['email' => 'testInit@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest', 'token' => 're' . $token]);
        $response->seeStatusCode(422);

        //perform correct reset
        $response = $this->json('POST', '/password/reset', ['email' => 'testInit@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest', 'token' => $token]);
        $response->seeStatusCode(200);
        $this->json('GET', '/auth/logout');
        $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $response->seeStatusCode(422);
        $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'testtest']);
        $response->seeStatusCode(200);
    }

    public function testErrorResetPassword()
    {
        //perform restore request
        $response = $this->json('POST', '/password/email', ['email' => 'AtestInit@test.com']);
        $response->seeStatusCode(422);
    }

    public function testThrottlesLogins()
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'atest']);
            $response->seeStatusCode(422);
        }
        $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $response->seeStatusCode(422);

    }

    public function testNoThrottlesLogins()
    {
        for ($i = 0; $i < 4; $i++) {
            $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'atest']);
            $response->seeStatusCode(422);
        }
        $response = $this->json('POST', '/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $response->seeStatusCode(200);

    }
}
