<?php

namespace Companies\Company;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Support\testing\ActingAs;

class CompanysControllerTest extends \TestCase
{
    use DatabaseTransactions, ActingAs;

    public function setUp()
    {
        parent::setUp();
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);
    }


    public function testIndex()
    {
        /**
         * @var $company \plunner\Company
         */
        $company = \plunner\Company::findOrFail(1);
        $response = $this->actingAs($company)->json('GET', '/companies/company');
        $response->assertResponseOk();
        $response->seeJsonEquals($company->toArray());
    }

    public function testErrorIndex()
    {
        $response = $this->json('GET', '/companies/company');
        $response->seeStatusCode(401);
    }

    public function testUpdate()
    {
        $company = \plunner\Company::findOrFail(1);
        $data = [
            'name' => 'test',
            'password' => 'testest',
            'password_confirmation' => 'testest',
        ];

        //correct request
        $response = $this->actingAs($company)->json('PUT', '/companies/company/', $data);
        $response->assertResponseOk();
        $data2 = $data;
        unset($data2['password']);
        unset($data2['password_confirmation']);
        $response->seeJson($data2);

        //no correct request
        unset($data['password_confirmation']);
        $response = $this->actingAs($company)->json('PUT', '/companies/company/', $data);
        $response->seeStatusCode(422);
    }
}
