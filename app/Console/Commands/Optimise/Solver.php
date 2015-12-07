<?php
/**
 * Created by PhpStorm.
 * User: Claudio Cardinale <cardi@thecsea.it>
 * Date: 07/12/15
 * Time: 21.18
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace plunner\Console\Commands\Optimise;

use Illuminate\Console\Scheduling\Schedule;

/**
 * Class Solver
 * @package plunner\Console\Commands\Optimise
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class Solver
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string[]
     */
    private $users;
    /**
     * @var string[]
     */
    private $meetings;
    /**
     * @var int
     */
    private $timeSlots = 0;
    /**
     * @var int
     */
    private $maxTimeSlots = 0;
    /**
     * @var string[]
     */
    private $meetingsAvailability;
    /**
     * @var string[]
     */
    private $meetingsDuration;
    /**
     * @var string[]
     */
    private $usersAvailability;
    /**
     * @var string[]
     */
    private $usersMeetings;

    /*
    * @var Schedule laravel schedule object needed to perform command in background
    */
    private $schedule;

    const ARRAY_PROPRIETIES = ['users', 'meetings', 'meetingsAvailability', 'meetingsDuration', 'usersAvailability', 'usersMeetings'];
    const INT_PROPRIETIES = ['timeSlots', 'maxTimeSlots'];

    //TODo clone function
    //TODO mehtod to check if all variables are correctly set
    //TODO check no duplicates
    //TODO exception if glpsol return erros


    /**
     * Solver constructor.
     * @throws OptimiseException on problems during creation of tmp dir
     */
    public function __construct(Schedule $schedule)
    {
        $this->createPath();
        $this->schedule = $schedule;
    }

    /**
     * @throws OptimiseException on problems during creation of tmp dir
     */
    private function createPath()
    {
        $this->path = tempnam(sys_get_temp_dir(), 'OPT');
        mkdir($this->path);
        if(! is_dir($this->path))
            throw new OptimiseException('problem during creation of tmp dir');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param Schedule $schedule
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @return \string[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param \string[] $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return \string[]
     */
    public function getMeetings()
    {
        return $this->meetings;
    }

    /**
     * @param \string[] $meetings
     */
    public function setMeetings($meetings)
    {
        $this->meetings = $meetings;
    }

    /**
     * @return int
     */
    public function getTimeSlots()
    {
        return $this->timeSlots;
    }

    /**
     * @param int $timeSlots
     * @throws OptimiseException
     */
    public function setTimeSlots($timeSlots)
    {
        foreach($timeSlots as $timeSlot) {
            if(!is_int($timeSlot) || $timeSlot <=0)
                throw new OptimiseException('$timeSlots is not integer or it is not >0');
        }

        $this->timeSlots = $timeSlots;
    }

    /**
     * @return int
     */
    public function getMaxTimeSlots()
    {
        return $this->maxTimeSlots;
    }

    /**
     * @param int $maxTimeSlots
     */
    public function setMaxTimeSlots($maxTimeSlots)
    {
        $this->maxTimeSlots = $maxTimeSlots;
    }

    /**
     * @return \string[]
     */
    public function getMeetingsAvailability()
    {
        return $this->meetingsAvailability;
    }

    /**
     * @param \string[] $meetingsAvailability
     * @throws OptimiseException
     */
    public function setMeetingsAvailability($meetingsAvailability)
    {
        $meetings = array_keys($meetingsAvailability);
        if($meetings != $this->meetings)
            throw new OptimiseException('meetings different from meetings set');
        foreach($meetingsAvailability as $meetingsAvailabilityS) {
            $timeSlots = array_keys($meetingsAvailabilityS);
            if($timeSlots != $this->timeSlots)
                throw new OptimiseException('timeSlots different from timeSlots set');
        }

        $this->meetingsAvailability = $meetingsAvailability;
    }

    /**
     * @return \string[]
     */
    public function getMeetingsDuration()
    {
        return $this->meetingsDuration;
    }

    /**
     * @param \string[] $meetingsDuration
     * @throws OptimiseException
     */
    public function setMeetingsDuration($meetingsDuration)
    {
        $meetings = array_keys($meetingsDuration);
        if($meetings != $this->meetings)
            throw new OptimiseException('meetings different from meetings set');
        foreach($meetingsDuration as $duration) {
            if(is_int($duration) && $duration >0)
                throw new OptimiseException('duration is not integer or it is not >0');
        }

        $this->meetingsDuration = $meetingsDuration;
    }

    /**
     * @return \string[]
     */
    public function getUsersAvailability()
    {
        return $this->usersAvailability;
    }

    /**
     * @param \string[] $usersAvailability
     * @throws OptimiseException
     */
    public function setUsersAvailability($usersAvailability)
    {
        $users = array_keys($usersAvailability);
        if($users != $this->users)
            throw new OptimiseException('users different from users set');
        foreach($usersAvailability as $usersAvailabilityS) {
            $timeSlots = array_keys($usersAvailabilityS);
            if($timeSlots != $this->timeSlots)
                throw new OptimiseException('timeSlots different from timeSlots set');
        }

        $this->usersAvailability = $usersAvailability;
    }

    /**
     * @return \string[]
     */
    public function getUsersMeetings()
    {
        return $this->usersMeetings;
    }

    /**
     * @param \string[] $usersMeetings
     * @throws OptimiseException
     */
    public function setUsersMeetings($usersMeetings)
    {
        $users = array_keys($usersMeetings);
        if($users != $this->users)
            throw new OptimiseException('users different from users set');
        foreach($usersMeetings as $usersMeetingsS) {
            $meetings = array_keys($usersMeetingsS);
            if($meetings != $this->meetings)
                throw new OptimiseException('meetings different from meetings set');
        }

        $this->usersMeetings = $usersMeetings;
    }

    /**
     * @throws OptimiseException
     */
    private function writeUsers()
    {
        self::writeCSVArray($this->getUsersPath(), $this->users, 'Users');
    }

    /**
     * @throws OptimiseException
     */
    private function writeMeetings()
    {
        self::writeCSVArray($this->getMeetingsPath(), $this->meetings, 'Meetings');
    }

    /**
     * @throws OptimiseException
     */
    private function writeMeetingsDuration()
    {
        self::writeCSVArray($this->getMeetingsDurationsPath().'/MeetingsDuration.csv', $this->meetingsDuration, 'MeetingsDuration');
    }

    /**
     * @throws OptimiseException
     */
    private function writeMeetingsAvailability()
    {
        self::writeCSVMatrix($this->getMeetingsAvailabilityPath().'/MeetingsAvailability.csv', $this->meetingsAvailability, 'MeetingsAvailability');
    }

    /**
     * @throws OptimiseException
     */
    private function writeUsersAvailability()
    {
        self::writeCSVMatrix($this->getUsersAvailabilityPath().'/UsersAvailability.csv', $this->usersAvailability, 'UsersAvailability');
    }

    /**
     * @throws OptimiseException
     */
    private function writeUsersMeetings()
    {
        self::writeCSVMatrix($this->getUsersMeetingsPath().'/UsersMeetings.csv', $this->usersMeetings, 'UsersMeetings');
    }

    /**
     * @param string $file
     * @param array $data
     * @param string $name
     * @throws OptimiseException
     */
    static private function writeCSVArray($file, $data, $name)
    {
        $f = function ($fp, $data){
            foreach ($data as $key=>$field) {
                fputcsv($fp, [$key, $field]);
            }
        };

        self::writeCSV($file, $data, ['i', $name], $f);
    }

    /**
     * @param string $file
     * @param array $data
     * @param string $name
     * @throws OptimiseException
     */
    static private function writeCSVMatrix($file, $data, $name)
    {
        $f = function ($fp, $data){
            foreach ($data as $key=>$field) {
                foreach ($field as $key2=>$field2)
                    fputcsv($fp, [$key, $key2, $field2]);
            }
        };

        self::writeCSV($file, $data, ['i', 'j', $name], $f);
    }

    /**
     * @param string $file
     * @param array $data
     * @param array $heading
     * @param \Closure $writer
     * @throws OptimiseException
     */
    static private function writeCSV($file, $data, $heading, \Closure $writer)
    {
        $fp = @fopen($file, 'w');
        if(!$fp)
            throw new OptimiseException('problem during creation of a file');

        fputcsv($fp, $heading);

        $writer($fp, $data);

        fclose($fp);
    }

    /**
     * @throws OptimiseException
     */
    public function solve()
    {
        $this->writeData();
        $this->writeModelFile();
        //TODO ...
    }

    /**
     * @throws OptimiseException
     */
    private function writeModelFile()
    {
        $strReplaceS = array('{USERS_PATH}', '{MEETINGS_PATH}', '{USER_AVAILABILITY_PATH}', '{MEETINGS_AVAILABILITY_PATH}', '{USER_MEETINGS_PATH}', '{MEETINGS_DURATION_PATH}, {TIME_SLOTS}, {MAX_TIME_SLOTS}');
        $strReplaceR = array($this->getUsersPath(), $this->getMeetingsPath(), $this->getUsersAvailabilityPath(), $this->getMeetingsAvailabilityPath(), $this->getUsersMeetingsPath(), $this->getMeetingsDurationPath(), $this->timeSlots, $this->maxTimeSlots);
        $f = fopen($this->getModelPath(), "w");
        if(!$f)
            throw new OptimiseException('problem during creation of a file');
        fwrite($f, str_replace($strReplaceS, $strReplaceR, file_get_contents(__DIR__ . "/model.stub")));
        fclose($f);
    }

    /**
     * @return string
     */
    private function getModelPath()
    {
        return $this->path.'/model.mod';
    }

    /**
     * @return string
     */
    private function getUsersPath()
    {
        return $this->path.'/Users.csv';
    }

    /**
     * @return string
     */
    private function getMeetingsPath()
    {
        return $this->path.'/Meeting.csv';
    }

    /**
     * @return string
     */
    private function getMeetingsDurationPath()
    {
        return $this->path.'/MeetingsDuration.csv';
    }

    /**
     * @return string
     */
    private function getMeetingsAvailabilityPath()
    {
        return $this->path.'/MeetingsAvailability.csv';
    }

    /**
     * @return string
     */
    private function getUsersAvailabilityPath()
    {
        return $this->path.'/UsersAvailability.csv';
    }

    /**
     * @return string
     */
    private function getUsersMeetingsPath()
    {
        return $this->path.'/UsersMeetings.csv';
    }

    /**
     * @throws OptimiseException
     */
    private function writeData()
    {
        $this->checkData();
        $this->writeUsers();
        $this->writeMeetings();
        $this->writeMeetingsDuration();
        $this->writeMeetingsAvailability();
        $this->writeUsersAvailability();
        $this->writeUsersMeetings();
    }

    /**
     * @throws OptimiseException
     */
    private function checkData()
    {
        $this->checkArrayProprieties(self::ARRAY_PROPRIETIES);
        $this->checkIntProprieties(self::INT_PROPRIETIES);
    }

    /**
     * @param $proprieties
     * @throws OptimiseException
     */
    private function checkArrayProprieties($proprieties)
    {
        foreach($proprieties as $propriety)
            if(count($this->$propriety)==0)
                throw new OptimiseException($propriety.' is not set correctly');
    }

    /**
     * @param $proprieties
     * @throws OptimiseException
     */
    private function checkIntProprieties($proprieties)
    {
        foreach($proprieties as $propriety)
            if(!is_int($this->$propriety) || $this->$propriety <= 0)
                throw new OptimiseException($propriety.' is not set correctly');
    }
}