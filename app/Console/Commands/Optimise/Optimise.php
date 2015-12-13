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
        $this->startTime = new \DateTime();
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
        $solver = $this->setUserAvailability($solver);
        $solver = $this->setMeetingsAvailability($solver);
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
        //this is not the most efficient way, but the simplest. The best way is using directly sql
        $meetings = $this->company->groups()->with('meetings')
            ->whereHas('meetings.timeslots',function($query){return $this->timeSlotsFilter($query);})
            ->get()->pluck('meetings')->collapse()->pluck('id')->toArray();
        return $solver->setMeetings($meetings);
    }

    /**
     * @param Solver $solver
     * @return Solver
     * @throws OptimiseException
     */
    private function setUserAvailability(Solver $solver)
    {
        //this is not the most efficient way, but the simplest. The best way is using directly sql

        //get timeslots from db
        $timeSlots = $this->company->employees()->with('calendars.timeslots')
            ->whereHas('calendars',function($query){ //enable filter
                $query->where('enabled','=','1');
            })->whereHas('calendars.timeslots',function($query){return $this->timeSlotsFilter($query);})->get(); //TODO do in a better way
        //get only timeslots data
        $timeSlots = $timeSlots->pluck('calendars','id')
            ->map(function($item, $key){ //collapse timeslots
                return $item->pluck('timeslots')->collpase()
                    ->map(function($item, $key){ //convert timeslots
                        $this->timeSlotsConverter($item);
                });
            });

        //set solver
        return $solver->setUsersAvailability(self::getAvailabilityArray($timeSlots));
    }

    private function setMeetingsAvailability(Solver $solver)
    {
        //this is not the most efficient way, but the simplest. The best way is using directly sql

        //TODO use meeting list
        //get timeslots from db
        $timeSlots = $this->company->groups()
            ->with('meetings.timeslots')
            ->whereHas('meetings.timeslots',function($query){return $this->timeSlotsFilter($query);})->get(); //TODO do in a better way
        //get only timeslots data
        $timeSlots = $timeSlots->pluck('meetings')->collapse()->pluck('timeslots','id')
            ->map(function($item, $key){ //convert timeslots
                $this->timeSlotsConverter($item);
            });

        return $solver->setMeetingsAvailability(self::getAvailabilityArray($timeSlots));
    }

    private function setMeetingsDuration(Solver $solver)
    {
        //this is not the most efficient way, but the simplest. The best way is using directly sql

        $timeSlots = $this->company->groups()
            ->with('meetings')
            ->whereHas('meetings.timeslots',function($query){return $this->timeSlotsFilter($query);})->get(); //TODO do in a better way
        //get only timeslots data
        $timeSlots = $timeSlots->pluck('meetings')->collapse()->pluck('timeslots','id')
            ->map(function($item, $key){ //convert timeslots
                $this->timeSlotsConverter($item);
            });

        //TODO implement inside availability
        return $solver->setMeetingsAvailability(self::getAvailabilityArray($timeSlots));
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
            $ret = self::fillZero($ret, $id);
        }

        return $ret;
    }

    private function timeSlotsConverter($item)
    {
        $item->time_start = $this->toTimeSlot($item->time_start);
        $item->time_end = $this->toTimeSlot($item->time_end);
        //TODO try catch
    }

    private function timeSlotsFilter($query)
    {
        $query->where('time_start','>=',$this->startTime);
        $query->where('time_end','<=',$this->endTime);
    }

    static private function fillTimeSlots($array, $id, $timeSlots)
    {
        foreach($timeSlots as $timeSlot)
        {
            $array[$id] = self::arrayPadInterval($array[$id], $timeSlot->time_start, $timeSlot->time_end);
        }
        return $array;
    }

    static private function fillZero($array, $id)
    {
        for($i = 0; $i < self::TIME_SLOTS; $i++){
            if(!isset($array[$id][$i]))
                $array[$id][$i] = 0;
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