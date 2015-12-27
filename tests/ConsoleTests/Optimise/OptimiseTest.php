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
        $optimise = new Optimise($company, new Schedule(), \App::getInstance());
        $optimise->setTimeSlots(4);
        $optimise->setMaxTimeSlots(4);
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

        $meeting1 = factory(\plunner\Meeting::class)->make(['duration'=>1*Optimise::TIME_SLOT_DURATION]);
        $group1->meetings()->save($meeting1);
        $meeting2 = factory(\plunner\Meeting::class)->make(['duration'=>3*Optimise::TIME_SLOT_DURATION]);
        $group2->meetings()->save($meeting2);

        $now = new \DateTime();
        $timeslots1 = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now)];
        $timeslots2 = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now, 3)];
        $meeting1->timeslots()->create($timeslots1);
        $meeting2->timeslots()->create($timeslots2);
        $timeslotsE = ['time_start' => self::addTimeInterval(clone $now, 3), 'time_end'=>self::addTimeInterval(clone $now, $optimise->getTimeSlots())];
        $timeslotsENo = ['time_start' => self::addTimeInterval(clone $now, 1), 'time_end'=>self::addTimeInterval(clone $now, $optimise->getTimeSlots())];
        $employees->each(function($employee) use ($timeslotsE){
            $employee->calendars()->first()->timeslots()->create($timeslotsE);
        });
        $employeeNo->calendars()->first()->timeslots()->create($timeslotsENo);

        $optimise->setStartTime(clone $now);
        $optimise->optimise();

        $x = [];
        foreach($employees as $employee)
            $x[$employee->id] = [$meeting1->id=>0,$meeting2->id=>1];
        $x[$employeeNo->id] = [$meeting1->id=>1,$meeting2->id=>0];

        $this->assertEquals($x, $optimise->getSolver()->getXResults());

        $y = [];
        $y[$meeting1->id] = [1=>1,0,0,0];
        $y[$meeting2->id] = [1=>1,0,0,0];

        $this->assertEquals($y, $optimise->getSolver()->getYResults());

        //save results in db
        $this->assertEquals(NULL, $meeting1->fresh()->start_time);
        $optimise->save();
        $this->assertEquals($now, new \DateTime($meeting1->fresh()->start_time));
        $this->assertEquals($now, new \DateTime($meeting2->fresh()->start_time));
        foreach($employees as $employee)
            $this->assertEquals([$meeting2->id], $employee->meetings->pluck('id')->toArray());
        $this->assertEquals([$meeting1->id], $employeeNo->meetings->pluck('id')->toArray());
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
