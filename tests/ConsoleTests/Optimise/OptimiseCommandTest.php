<?php

namespace ConsoleTests\Optimise;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class OptimiseCommandTest extends \TestCase
{
    use DatabaseTransactions;


    //This is teh same model of SolverTest
    /**
     * @param \DateTime $date
     * @param Int $multiplier
     * @return \DateTime
     */
    static private function addTimeInterval(\DateTime $date, $multiplier = 1)
    {
        return $date->add(new \DateInterval('PT' . config('app.timeslots.duration') * $multiplier . 'S'));
    }

    public function testSimpleModel()
    {
        if (!$this->doConsole())
            return;

        //create data
        ob_start();
        $status = \Artisan::call('db:seed', [
            '--class' => 'OptimisationDemoSeeder',
            '--force' => true
        ]);
        ob_end_clean();
        $this->assertEquals(0, $status);

        //get company
        $company = \plunner\Company::all()->last();

        //optimise
        $status = \Artisan::call('optimise:meetings', [
            'companyId' => $company->id
        ]);
        $this->assertEquals(0, $status);

        //check if the meetings are correctly optimised looking the users number that attend to them
        $meetingShort = \plunner\Meeting::whereIn('group_id', $company->groups->pluck('id'))->where(
            'duration', config('app.timeslots.duration'))->firstOrfail();
        $meetingLong = \plunner\Meeting::whereIn('group_id', $company->groups->pluck('id'))->where(
            'duration', config('app.timeslots.duration') * 3)->firstOrfail();
        $this->assertEquals(1, $meetingShort->employees->count());
        $this->assertEquals(2, $meetingLong->employees->count());
    }
}
