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
    const TIME_SLOT_DURATION = 900; //seconds -> 15 minutes
    const TIME_SLOTS = 672; //total amount of timeslots that must be optimised -> one week 4*24*7

    //TODO timezone
    //TODO fix here
    /**
     * @var \DateTime
     */
    private $startTime;
    /**
     * @var \DateTime
     */
    private $endTime;

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

        //TODO tmp
        $this->startTime = new \DateTime(); //TODO this must be a precise time every 15 minutes
        $this->endTime = clone $this->startTime;
        $this->endTime->add(new \DateInterval('P7D'));
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
        $solver = $this->setData($solver);
        $solver = $solver->solve();
        print_r($solver->getXResults());
        print_r($solver->getYResults());
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
        $solver = $this->setAllMeetingsInfo($solver);
        $solver = $this->setUserAvailability($solver);
        $solver = $this->setUsersMeetings($solver);
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
        //since we consider busy timeslots, we need to get all users
        $users = $this->company->employees->pluck('id')->toArray();
        return $solver->setUsers($users);
    }

    /**
     * @param Solver $solver
     * @return Solver
     */
    private function setAllMeetingsInfo(Solver $solver)
    {
        /**
         * @var $meetings \Illuminate\Support\Collection
         */
        $meetings = collect($this->company->getMeetingsTimeSlots($this->startTime, $this->endTime));
        $timeslots = $meetings->groupBy('id')>map(function($item, $key) { //convert timeslots
                $this->timeSlotsConverter($item);
            });
        return $solver->setMeetings($timeslots->keys())
            ->setMeetingsDuration($meetings->pluck('duration','id'))
            ->setMeetingsAvailability(self::getAvailabilityArray($timeslots));
    }

    /**
     * @param Solver $solver
     * @return Solver
     * @throws OptimiseException
     */
    private function setUserAvailability(Solver $solver)
    {
        /**
         * @var $users \Illuminate\Support\Collection
         */
        $users = collect($this->company->getEmployeesTimeSlots($this->startTime, $this->endTime));
        $timeslots = $users->groupBy('id')>map(function($item, $key) { //convert timeslots
                $this->timeSlotsConverter($item);
            });
        return $solver->setUsersAvailability(self::getAvailabilityArray($timeslots));
    }

    /**
     * @param Solver $solver
     * @return Solver
     * @throws OptimiseException
     */
    private function setUsersMeetings(Solver $solver)
    {
        $users = $solver->getUsers();
        $meetings = $solver->getMeetings();
        /**
         * @var $usersMeetings \Illuminate\Support\Collection
         */
        $usersMeetings = collect($this->company->getUsersMeetings($users, $meetings))->groupBy('employee_id');

        return $solver->setUsersAvailability(self::getUsersMeetingsArray($usersMeetings));
    }

    /**
     * @param array $users
     * @param array $meetings
     * @param \Illuminate\Support\Collection $usersMeetings
     * @return array
     */
    static private function getUsersMeetingsArray($users, $meetings, $usersMeetings)
    {
        $ret = [];
        foreach($users as $user)
        {
            $usersMeetingsTmp = $usersMeetings->get($user);
            foreach($meetings as $meeting){
                if($usersMeetingsTmp->contains('meeting_id', $meeting)){
                    $ret[$user][$meeting] = 1;
                }else{
                    $ret[$user][$meeting] = 0;
                }
            }
        }

        return $ret;
    }

    private function timeSlotsConverter($item)
    {
        $item->time_start = $this->toTimeSlot($item->time_start);
        $item->time_end = $this->toTimeSlot($item->time_end);
        //TODO try catch
    }

    /**
     * @param \Illuminate\Support\Collection $timeSlots
     * @return array
     */
    static private function getAvailabilityArray($timeSlots)
    {
        $ret = [];
        foreach($timeSlots as $id=>$timeSlots2)
        {
            $ret = self::fillTimeSlots($ret, $id, $timeSlots2);
            $ret = self::fillRow($ret, $id, '1');
        }

        return $ret;
    }

    static private function fillTimeSlots($array, $id, $timeSlots)
    {
        foreach($timeSlots as $timeSlot) {
            $array[$id] = self::arrayPadInterval($array[$id], $timeSlot->time_start, $timeSlot->time_end);
        }
        return $array;
    }

    static private function fillRow($array, $id, $fill = '0')
    {
        for($i = 0; $i < self::TIME_SLOTS; $i++){
            if(!isset($array[$id][$i]))
                $array[$id][$i] = $fill;
        }

        return $array;
    }

    static private function arrayPadInterval($array, $from, $to, $pad = '0')
    {
        for($i = $from; $i<=$to; $i++)
            $array[$i] = $pad;
        return $array;
    }


    /**
     * @param $time
     * @return int
     * @throws OptimiseException
     */
    private function toTimeSlot($time)
    {
        $dateTime = new \DateTime($time);
        $diff = $dateTime->sub($this->startTime);
        $diff = explode(':',$diff->format('R:d:h:i:s'));
        if($diff[0] != '+')
            throw new OptimiseException('timeslot time <= startTime');
        //TODO check upper limit
        $diff = $diff[1]*86400 + $diff[2]*3600 + $diff[3]*60 + $diff[4];
        return (int)round($diff/self::TIME_SLOT_DURATION); //TODO can round cause overlaps?
    }
}