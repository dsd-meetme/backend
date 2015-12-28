<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 12/12/15
 * Time: 15.41
 */

namespace plunner\Console\Commands\Optimise;

use Illuminate\Console\Scheduling\Schedule;
use plunner\company;
use plunner\Events\Optimise\ErrorEvent;
use plunner\Events\Optimise\OkEvent;

/**
 * Class Optimise
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @package plunner\Console\Commands\Optimise
 */
class Optimise
{
    //TODo max timeslots can be an environment var
    const TIME_SLOT_DURATION = 900; //seconds -> 15 minutes
    const DEFAULT_MAX_TIME_SLOTS = 20; //max duration of a meeting in term of timeslots //20
    const DEFAULT_TIME_SLOTS = 672;  //total amount of timeslots that must be optimised -> one week 4*24*7 = 672

    private $max_time_slots = self::DEFAULT_MAX_TIME_SLOTS;
    private $time_slots = self::DEFAULT_TIME_SLOTS;

    //TODO timezone
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
     * @var \Illuminate\Contracts\Foundation\Application;
     */
    private $laravel;

    /**
     * @var Solver
     */
    private $solver = null;

    //TODO clone
    //TODO to_string

    /**
     * Optimise constructor.
     * @param company $company
* @param Schedule $schedule
     * @param \Illuminate\Contracts\Foundation\Application $laravel
     */
    public function __construct(company $company, Schedule $schedule, \Illuminate\Contracts\Foundation\Application $laravel)
    {
        $this->company = $company;
        $this->schedule = $schedule;
        $this->laravel = $laravel;

        $this->setStartTime((new \DateTime())->modify('next monday'));
    }


    /**
     * @param \DateTime $startTime
     */
    public function setStartTime(\DateTime $startTime)
    {
        $this->startTime = clone $startTime;
        $this->endTime = clone $this->startTime;
        $this->endTime->add(new \DateInterval('PT'.(($this->max_time_slots+$this->time_slots)*self::TIME_SLOT_DURATION).'S'));
    }

    /**
     * @return int
     */
    public function getMaxTimeSlots()
    {
        return $this->max_time_slots;
    }

    /**
     * @param int $max_time_slots
     */
    public function setMaxTimeSlots($max_time_slots)
    {
        $this->max_time_slots = $max_time_slots;
    }

    /**
     * @return int
     */
    public function getTimeSlots()
    {
        return $this->time_slots;
    }

    /**
     * @param int $time_slots
     */
    public function setTimeSlots($time_slots)
    {
        $this->time_slots = $time_slots;
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

    /**
     * @return Solver
     */
    public function getSolver()
    {
        return $this->solver;
    }


    /**
     * @return Optimise
     */
    public function optimise()
    {
        try {
            $solver = new Solver($this->schedule, $this->laravel);
            $solver = $this->setData($solver);
            $solver = $solver->solve();
            $this->solver = $solver;
        }catch(\Exception $e)
        {
            \Event::fire(new ErrorEvent($this->company, $e->getMessage()));
            throw new OptimiseException('Optimising error', 0, $e);
            //TODO catch specif exception
        }
        return $this;
    }

    /**
     * @return Optimise
     */
    public function save()
    {
        if(!($this->solver instanceof Solver)) {
            \Event::fire(new ErrorEvent($this->company, 'solver is not an instace of Solver'));
            throw new OptimiseException('solver is not an instance of Solver');
            return;
        }
        //TODO check results before save them

        try {
            $this->saveMeetings($this->solver);
            $this->saveEmployeesMeetings($this->solver);
        }catch(\Exception $e)
        {
            \Event::fire(new ErrorEvent($this->company, $e->getMessage()));
            throw new OptimiseException('Optimising error', 0, $e);
            //TODO catch specif exception
        }
        //TODO Is this the correct place?
        \Event::fire(new OkEvent($this->company));
        return $this;
    }

    //TODO fix php doc with exceptions

    /**
     * @param Solver $solver
     */
    private function saveMeetings(Solver $solver)
    {
        $meetings = $solver->getYResults();
        foreach($meetings as $id=>$meeting){
            $meetingO = \plunner\Meeting::findOrFail($id);//TODO catch error
            $meetingO->start_time = $this->toDateTime(array_search('1', $meeting));
            $meetingO->save();
        }
    }

    /**
     * @param Solver $solver
     */
    private function saveEmployeesMeetings(Solver $solver)
    {
        $employeesMeetings = $solver->getXResults();
        foreach($employeesMeetings as $eId =>$employeeMeetings)
        {
            $employee = \plunner\Employee::findOrFail($eId);
            $employeeMeetings = collect($employeeMeetings);
            $employeeMeetings = $employeeMeetings->filter(function ($item) {
                return $item == 1;
            });
            $employee->meetings()->attach($employeeMeetings->keys()->toArray());
        }
    }


    /**
     * @param Solver $solver
     * @return Solver
     * @throws OptimiseException
     */
    private function setData(Solver $solver)
    {
        $solver = $this->setTimeSlotsSolver($solver);
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
    private function setTimeSlotsSolver(Solver $solver)
    {
        return $solver->setTimeSlots($this->time_slots)->setMaxTimeSlots($this->max_time_slots);
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
        $timeslots = $meetings->groupBy('id')->map(function($item) { //convert timeslots
                return $this->durationConverter($this->timeSlotsConverter($item));
            });
        return $solver->setMeetings($timeslots->keys()->toArray())
            ->setMeetingsDuration($meetings->pluck('duration','id')->toArray())
            ->setMeetingsAvailability(self::getAvailabilityArray($timeslots, $this->time_slots));
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
        $timeslots = $users->groupBy('id')->map(function($item) { //convert timeslots
                return $this->timeSlotsConverter($item);
            });
        return $solver->setUsersAvailability(self::getAvailabilityArray($timeslots, $this->time_slots, false));
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

        return $solver->setUsersMeetings(self::getUsersMeetingsArray($users, $meetings, $usersMeetings));
    }

    /**
     * @param array $users
     * @param array $meetings
     * @param \Illuminate\Support\Collection $usersMeetings
     * @return array
     */
    static private function getUsersMeetingsArray($users, $meetings, \Illuminate\Support\Collection $usersMeetings)
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

    /**
     * @param mixed $item
     * @return mixed
     */
    private function durationConverter($item)
    {
        return $item->each(function($item2){
            $item2->duration = $this->convertDuration((int) $item2->duration);
            return $item2;
            //TODO try catch
        });
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    private function timeSlotsConverter($item)
    {
        return $item->each(function($item2){
            $item2->time_start = $this->toTimeSlot($item2->time_start);
            $item2->time_end = $this->toTimeSlot($item2->time_end);
            return $item2;
            //TODO try catch
        });
    }

    /**
     * @param \Illuminate\Support\Collection $timeSlots
     * @param bool|true $free if true the array is filled with 1 for timeslots values else with 0 for timeslots values
     * @return array
     */
    static private function getAvailabilityArray(\Illuminate\Support\Collection $timeSlots, $timeslotsN, $free=true)
    {
        $ret = [];
        foreach($timeSlots as $id=>$timeSlots2)
        {
            $ret = self::fillTimeSlots($ret, $id, $timeSlots2, $free?'1':'0');
            $ret = self::fillRow($ret, $id, $timeslotsN, $free?'0':'1');
        }

        return $ret;
    }

    /**
     * @param array $array
     * @param int $id
     * @param \Illuminate\Support\Collection $timeSlots
     * @param string $fill
     * @return array
     */
    static private function fillTimeSlots(array $array, $id, \Illuminate\Support\Collection $timeSlots, $fill = '0')
    {
        foreach($timeSlots as $timeSlot) {
            if(!isset($array[$id]))
                $array[$id] = [];
            $array[$id] = self::arrayPadInterval($array[$id], $timeSlot->time_start, $timeSlot->time_end, $fill);
        }
        return $array;
    }

    /**
     * @param array $array
     * @param int $id
     * @param string $fill
     * @return array
     */
    static private function fillRow(array $array, $id, $until, $fill = '0')
    {
        for($i = 1; $i <= $until; $i++){
            if(!isset($array[$id][$i]))
                $array[$id][$i] = $fill;
        }

        return $array;
    }

    /**
     * @param array $array
     * @param int $from
     * @param int $to
     * @param string $pad
     * @return array
     */
    static private function arrayPadInterval(array $array, $from, $to, $pad = '0')
    {
        for($i = $from; $i<$to; $i++)
            $array[$i] = $pad;
        return $array;
    }


    /**
     * @param mixed $time
     * @return int
     * @throws OptimiseException
     */
    private function toTimeSlot($time)
    {
        $dateTime = new \DateTime($time);
        $diff = $dateTime->diff($this->startTime);
        $diff = explode(':',$diff->format('%R:%d:%h:%i:%s'));
        $diff = $diff[1]*86400 + $diff[2]*3600 + $diff[3]*60 + $diff[4];
        //if($diff[0] != '-' && $diff != 0)
          //  throw new OptimiseException('timeslot time <= startTime');
        //TODO fix check
        //TODO check if diff makes sense
        //TODO check upper limit
        return (int)(round($diff/self::TIME_SLOT_DURATION)+1); //TODO can round cause overlaps?
    }

    /**
     * @param int $timeslot
     * @return \DateTime
     */
    private function toDateTime($timeslot)
    {
        $ret = clone $this->startTime;
        return $ret->add(new \DateInterval('PT'.(($timeslot-1)*self::TIME_SLOT_DURATION).'S'));
    }

    /**
     * @param int $duration
     * @return int
     */
    static private function convertDuration($duration)
    {
        return (int)ceil($duration/self::TIME_SLOT_DURATION);
    }
}