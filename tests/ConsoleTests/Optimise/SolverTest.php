<?php

namespace ConsoleTests\Optimise;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use plunner\Console\Commands\Optimise\Solver;
use Illuminate\Console\Scheduling\Schedule;

class SolverTest extends \TestCase
{
    use DatabaseTransactions;


    public function testSimpleModel()
    {
        if(!$this->doConsole())
            return;
        $solver = new Solver(new Schedule(), \App::getInstance());
        $solver->setTimeSlots(4);
        $solver->setMaxTimeSlots(4);
        $solver->setUsers([1=>1,2,3]);
        $solver->setMeetings([1=>1,2]);
        $solver->setMeetingsDuration([1=>1,3]);
        $solver->setMeetingsAvailability([
            1=>[1=>1,0,0,0],
            [1=>1,1,1,0],
        ]);
        $solver->setUsersAvailability([
            1=>[1=>1,1,0,0],
            [1=>1,1,1,0],
            [1=>1,1,1,0],
        ]);
        $solver->setUsersMeetings([
            1=>[1=>1,0],
            [1=>1,1],
            [1=>1,1],
        ]);
        $solver->solve();
        $this->assertEquals([1=>[1=>1,0],[1=>0,1], [1=>0,1]],$solver->getXResults());
        $this->assertEquals([1=>[1=>1,0,0,0],[1=>1,0,0,0]],$solver->getYResults());
    }
}
