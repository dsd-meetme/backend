<?php

namespace Employees;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeesAuthTest extends \TestCase
{
    use DatabaseTransactions;

    public function testLogin()
    {
        $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInit' , 'email' => 'testEmp@test.com', 'password' => 'test']);
        $response->seeStatusCode(200);
    }

    public function testErrorLogin()
    {
        $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInit', 'email' => 'testEmp@test.com', 'password' => 'atest']);
        $response->seeStatusCode(422);
        $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInita' , 'email' => 'testEmp@test.com', 'password' => 'test']);
        $response->seeStatusCode(422);
    }

    public function testResetPassword()
    {
        //perform restore request
        $response = $this->json('POST', '/employees/password/email', ['company' => 'testInit', 'email' => 'testEmp@test.com']);
        $response->seeStatusCode(200);

        //get the token
        $token = \DB::table('password_resets_employees')->where('email', 'testEmp@test.com'.(\plunner\Company::whereName('testInit')->firstOrFail()->id))->value('token');

        //perform reset with error
        $response = $this->json('POST', '/employees/password/reset', ['company' => 'testInit', 'email' => 'atestEmp@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest', 'token' => 're' . $token]);
        $response->seeStatusCode(422);
        $response = $this->json('POST', '/employees/password/reset', ['company' => 'testInita', 'email' => 'testEmp@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest', 'token' => 're' . $token]);
        $response->seeStatusCode(422);

        //perform correct reset
        $response = $this->json('POST', '/employees/password/reset', ['company' => 'testInit', 'email' => 'testEmp@test.com', 'password_confirmation' => 'testtest', 'password' => 'testtest', 'token' => $token]);
        $response->seeStatusCode(200);
        $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInit', 'email' => 'testEmp@test.com', 'password' => 'test']);
        $response->seeStatusCode(422);
        $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInit', 'email' => 'testEmp@test.com', 'password' => 'testtest']);
        $response->seeStatusCode(200);
    }

    public function testErrorResetPassword()
    {
        $response = $this->json('POST', '/employees/password/email', ['company' => 'testInit', 'email' => 'AtestEmp@test.com']);
        $response->seeStatusCode(422);

        $response = $this->json('POST', '/employees/password/email', ['company' => 'testInita', 'email' => 'testEmp@test.com']);
        $response->seeStatusCode(422);
    }

    public function testThrottlesLogins()
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInit', 'email' => 'testEmp@test.com', 'password' => 'atest']);
            $response->seeStatusCode(422);
        }
        $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInit', 'email' => 'testEmp@test.com', 'password' => 'test']);
        $response->seeStatusCode(422);

    }

    public function testNoThrottlesLogins()
    {
        for ($i = 0; $i < 4; $i++) {
            $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInit', 'email' => 'testEmp@test.com', 'password' => 'atest']);
            $response->seeStatusCode(422);
        }
        $response = $this->json('POST', '/employees/auth/login', ['company' => 'testInit', 'email' => 'testEmp@test.com', 'password' => 'test']);
        $response->seeStatusCode(200);

    }
}
