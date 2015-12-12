<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 12/12/15
 * Time: 15.41
 */

namespace plunner\Console\Commands\Optimise;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use plunner\company;

/**
 * Class Optimise
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @package plunner\Console\Commands\Optimise
 */
class Optimise
{
    //TODO insert MAX timeslots limit during meeting creation
    const MAX_TIME_SLOTS = 20; //max duration of a meeting in term of timeslots
    const TIME_SLOT_DURATION = 15; //minutes
    const TIME_SLOTS = 672; //total amount of timeslots that must be optimised -> one week 4*24*7

    /**
     * @var Company
     */
    private $company;

    /**
    * @var Schedule laravel schedule object needed to perform command in background
    */
    private $schedule;

    /**
     * @var Appplication;
     */
    private $laravel;

    /**
     * Optimise constructor.
     * @param company $company
     * @param Schedule $schedule
     * @param Appplication $laravel
     */
    public function __construct(company $company, Schedule $schedule, Appplication $laravel)
    {
        $this->company = $company;
        $this->schedule = $schedule;
        $this->laravel = $laravel;
    }


    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function optmise()
    {
        //TODO ...
        $solver = new Solver($this->schedule, $this->laravel);
        $this->setData($solver);
        //TODO try...catch
    }

    /**
     * @param Solver $solver
     * @return Solver
     * @throws OptimiseException
     */
    private function setData(Solver $solver)
    {
        //TODO...
        //TODO get avalability only of this week

        $solver = $this->setTimeSlots($solver);
        $solver = $this->setUsers($solver);
        $solver = $this->setMeetings($solver);
        return $solver;
    }

    /**
     * @param Solver $solver
     * @return Solver
     * @throws OptimiseException
     */
    private function setTimeSlots(Solver $solver)
    {
        return $solver->setTimeSlots(self::TIME_SLOTS)->setMaxTimeSlots(self::MAX_TIME_SLOTS);
    }

    /**
     * @param Solver $solver
     * @return Solver
     */
    private function setUsers(Solver $solver)
    {
        $users = $this->company->employees->pluck('id')->toArray();
        return $solver->setUsers($users);
    }

    /**
     * @param Solver $solver
     * @return Solver
     */
    private function setMeetings(Solver $solver)
    {
        $meetings = $this->company->groups()->get()->pluck('meetings')->collapse()->pluck('id')->toArray();
        return $solver->setMeetings($meetings);
    }
}