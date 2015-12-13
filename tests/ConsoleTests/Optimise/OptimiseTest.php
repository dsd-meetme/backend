<?php

namespace ConsoleTests\Optimise;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use plunner\Console\Commands\Optimise\Optimise;
use plunner\Console\Commands\Optimise\Solver;
use Illuminate\Console\Scheduling\Schedule;

class OptimiseTest extends \TestCase
{
    use DatabaseTransactions;


    //This is teh same model of SolverTest
    public function testSimpleModel()
    {
        if(!$this->doConsole())
            return;
        $company = factory(\plunner\Company::class)->create();
        $employees = factory(\plunner\Employee::class, 3)->make()->each(function ($employee) use($company){
            $company->employees()->save($employee);
            $employee->calendars()->save(factory(\plunner\Calendar::class)->make());
        });

        $group1 = factory(\plunner\Group::class)->make();
        $group2 = factory(\plunner\Group::class)->make();
        $company->groups()->save($group1);
        $company->groups()->save($group2);
        $group1->employees()->attach($employees->pluck('id')->toArray());
        $employeeNo = $employees->pop();
        $group2->employees()->attach($employees->pluck('id')->toArray());

        $meeting1 = factory(\plunner\Meeting::class)->make();
        $group1->meetings()->save($meeting1);
        $meeting2 = factory(\plunner\Meeting::class)->make();
        $group2->meetings()->save($meeting2);

        $now = new \DateTime();
        $timeslots1 = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now)];
        $timeslots2 = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now, 3)];
        $meeting1->timeslots()->create($timeslots1);
        $meeting2->timeslots()->create($timeslots2);
        $timeslotsE = ['time_start' => self::addTimeInterval(clone $now, 4), 'time_end'=>self::addTimeInterval(clone $now, Optimise::TIME_SLOTS-1)];
        $timeslotsENo = ['time_start' => self::addTimeInterval(clone $now, 2), 'time_end'=>self::addTimeInterval(clone $now, Optimise::TIME_SLOTS-1)];
        $employees->each(function($employee) use ($timeslotsE){
            $employee->calendars()->first()->timeslots()->create($timeslotsE);
        });
        $employeeNo->calendars()->first()->timeslots()->create($timeslotsENo);

        //print_r($company->employees()->with('calendars.timeslots')->with('groups')->get()->toArray());
        //print_r($company->groups()->with('meetings.timeslots')->get()->toArray());
        // new Solver(new Schedule(), \App::getInstance());
        $optmise = new Optimise($company, new Schedule(), \App::getInstance());
        $optmise->startTime = $now;
        $optmise->optmise();
        //TODO set duration
       // $status = \Artisan::call('sync:caldav');
    }


    /**
     * @param \DateTime $date
     * @param Int $multiplier
     * @return \DateTime
     */
    static private function addTimeInterval(\DateTime $date, $multiplier=1)
    {
        return $date->add(new \DateInterval('PT'.Optimise::TIME_SLOT_DURATION*$multiplier.'S'));
    }
}
