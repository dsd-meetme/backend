<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Support\testing\ActingAs;

class ExampleControllerTest extends TestCase
{
    use DatabaseTransactions, ActingAs;

    public function testIndex()
    {
        config(['auth.model' => \plunner\User::class]);
        config(['jwt.user' => \plunner\User::class]);
        $user = \plunner\User::findOrFail(1);
        $response = $this->actingAs($user)->json('GET', '/companies/example');
        $response->seeStatusCode(200);
    }
}
