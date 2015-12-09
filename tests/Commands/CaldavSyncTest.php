<?php

namespace Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeesAuthTest extends \TestCase
{
    use DatabaseTransactions;

    public function testPerformSyncForeground()
    {
        $status = \Artisan::call('sync:caldav');
        $this->assertEquals(0, $status);
    }

    public function testPerformSyncBackground()
    {
        $status = \Artisan::call('sync:caldav', ['--background' => true]);
        $this->assertEquals(0, $status);
    }

    public function testError()
    {
        \Artisan::call('sync:caldav');
        $company = \plunner\Company::whereEmail('testInit@test.com')->firstOrFail();
        $employee = $company->employees()->whereEmail('testEmp@test.com')->firstOrFail();
        $calendar = $employee->calendars()->whereName('errors')->firstOrFail()->caldav;
        $this->assertNotEquals('', $calendar->sync_errors);
    }

    //TODO test a correct sync
}
