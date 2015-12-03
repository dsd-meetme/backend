<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlannedMeetingsTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /*public function testShowAllMeetingsInEmptyMonth()
    {
        $month = 11;

        $response = $this->actingAs($this->planner)->json('GET', '/employees/meetings/months/' . $month);

        $response->assertResponseOk();
        $response->seeJson([]);
    }

    public function testShowAllMeetingsInMonthWithOnlyRepeatingMeetings()
    {
        $this->assertTrue(true);
    }*/
}
