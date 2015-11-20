<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompaniesAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function testNewCompany()
    {
        $response = $this->json('POST', '/companies/auth/register', ['name' => 'test', 'email' => 'test@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest']);
        $response->seeStatusCode(200);
        $this->seeInDatabase("companies", ['name' => 'test', 'email' => 'test@test.com']);
        $token = json_decode($response->response->content(),true);
        $token = $token['token'];
        /**
         * @var $company \plunner\Company
         */
        $company = JWTAuth::authenticate($token);
        $this->assertEquals('test@test.com', $company->email);
    }

    public function testErrorNewCompany()
    {
        $response = $this->json('POST', '/companies/auth/register', ['name' => 'test', 'email' => 'test@test.com', 'password_confirmation' => 'atesttest', 'password' => 'testtest']);
        $response->seeStatusCode(422);
        $this->notSeeInDatabase("companies", ['name' => 'test', 'email' => 'test@test.com']);

    }

    public function testLogin()
    {
        $response = $this->json('POST', '/companies/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $response->seeStatusCode(200);
    }

    public function testErrorLogin()
    {
        $response = $this->json('POST', '/companies/auth/login', ['email' => 'testInit@test.com', 'password' => 'atest']);
        $response->seeStatusCode(422);
    }

    public function testResetPassword()
    {
        //perform restore request
        $response = $this->json('POST', '/companies/password/email', ['email' => 'testInit@test.com']);
        $response->seeStatusCode(200);

        //get the token
        $token = DB::table('password_resets_companies')->where('email', 'testInit@test.com')->value('token');

        //perform reset with error
        $response = $this->json('POST', '/companies/password/reset', ['email' => 'testInit@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest', 'token' => 're' . $token]);
        $response->seeStatusCode(422);

        //perform correct reset
        $response = $this->json('POST', '/companies/password/reset', ['email' => 'testInit@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest', 'token' => $token]);
        $response->seeStatusCode(200);
        $response = $this->json('POST', '/companies/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $response->seeStatusCode(422);
        $response = $this->json('POST', '/companies/auth/login', ['email' => 'testInit@test.com', 'password' => 'testtest']);
        $response->seeStatusCode(200);
    }

    public function testErrorResetPassword()
    {
        //perform restore request
        $response = $this->json('POST', '/companies/password/email', ['email' => 'AtestInit@test.com']);
        $response->seeStatusCode(422);
    }

    public function testThrottlesLogins()
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->json('POST', '/companies/auth/login', ['email' => 'testInit@test.com', 'password' => 'atest']);
            $response->seeStatusCode(422);
        }
        $response = $this->json('POST', '/companies/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $response->seeStatusCode(422);

    }

    public function testNoThrottlesLogins()
    {
        for ($i = 0; $i < 4; $i++) {
            $response = $this->json('POST', '/companies/auth/login', ['email' => 'testInit@test.com', 'password' => 'atest']);
            $response->seeStatusCode(422);
        }
        $response = $this->json('POST', '/companies/auth/login', ['email' => 'testInit@test.com', 'password' => 'test']);
        $response->seeStatusCode(200);

    }
}
