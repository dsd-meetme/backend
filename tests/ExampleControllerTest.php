<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Support\testing\ActingAs;

class ExampleControllerTest extends TestCase
{
    use DatabaseTransactions, ActingAs;

    public function testIndex()
    {
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);
        $company = \plunner\Company::findOrFail(1);
        $response = $this->actingAs($company)->json('GET', '/companies/example');
        $response->seeStatusCode(200);
    }

    public function testErrorIndex()
    {
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);
        $response = $this->json('GET', '/companies/example');
        $response->seeStatusCode(401);
    }
}
