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

use \Illuminate\Console\Scheduling\Schedule;
use \Illuminate\Foundation\Application;

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

    /**
     * @var Application
     */
    private $laravel;

    //TODo clone function
    //TODO mehtod to check if all variables are correctly set
    //TODO check no duplicates
    //TODO exception if glpsol return erros
    //TODO intercept all erros of system calls like mkdir


    /**
     * Solver constructor.
     * @param Schedule $schedule
     * @param Application $laravel
     * @throws OptimiseException on general problems
     */
    public function __construct(Schedule $schedule, Application $laravel)
    {
        self::checkGlpsol();
        $this->createPath();
        $this->schedule = $schedule;
        $this->laravel = $laravel;
    }

    /**
     * @throws OptimiseException
     */
    function __destruct()
    {
        if ($this->path && is_dir($this->path) && !self::delTree($this->path))
            throw new OptimiseException('problems during removing of path directory');
    }

    /**
     * @throws OptimiseException
     */
    static private function checkGlpsol()
    {
        if(!(`which glpsol`))
            throw new OptimiseException('glpsol is not installed');
    }

    /**
     * remove a no empty dir
     * @param $dir
     * @return bool
     */
    private static function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }


    /**
     * @throws OptimiseException on problems during creation of tmp dir
     */
    private function createPath()
    {
        $this->path = tempnam(sys_get_temp_dir(), 'OPT'); //TODO check the return in case of errors this return false on failure
        unlink($this->path); //remove file to create a dir
        if(file_exists($this->path))
            throw new OptimiseException('problem during creation of tmp dir (the directory already exists)');
        if(!@mkdir($this->path))
            throw new OptimiseException('problem during creation of tmp dir (mkdir problem)');;
        if(! is_dir($this->path))
            throw new OptimiseException('problem during creation of tmp dir (it is not possible to create directory)');
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
        if(!is_int($timeSlots) || $timeSlots <=0)
            throw new OptimiseException('$timeSlots is not integer or it is not >0');

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
     * @throws OptimiseException
     */
    public function setMaxTimeSlots($maxTimeSlots)
    {
        if(!is_int($maxTimeSlots) || $maxTimeSlots <=0)
            throw new OptimiseException('$maxTimeSlots is not integer or it is not >0');

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
        if(array_diff($meetings, $this->meetings))
            throw new OptimiseException('meetings different from meetings set');
        foreach($meetingsAvailability as $key=>$meetingsAvailabilityS) {
            $timeSlots = array_keys($meetingsAvailabilityS);
            if(count($timeSlots) != $this->timeSlots)
                throw new OptimiseException('timeSlots different from timeSlots set');
            $meetingsAvailability[$key] = self::arrayPad($meetingsAvailabilityS, $this->timeSlots + $this->maxTimeSlots, 0);
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
        if(array_diff($meetings, $this->meetings)) {
            print "";
            throw new OptimiseException('meetings different from meetings set');
        }
        foreach($meetingsDuration as $duration) {
            if(!is_int($duration) || $duration <=0)
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
        if(array_diff($users, $this->users))
            throw new OptimiseException('users different from users set');
        foreach($usersAvailability as $key=>$usersAvailabilityS) {
            $timeSlots = array_keys($usersAvailabilityS);
            if(count($timeSlots) != $this->timeSlots)
                throw new OptimiseException('timeSlots different from timeSlots set');

            $usersAvailability[$key] = self::arrayPad($usersAvailabilityS, $this->timeSlots + $this->maxTimeSlots, 0);
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
        if(array_diff($users, $this->users))
            throw new OptimiseException('users different from users set');
        foreach($usersMeetings as $usersMeetingsS) {
            $meetings = array_keys($usersMeetingsS);
            if(array_diff($meetings, $this->meetings))
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
        self::writeCSVArray($this->getMeetingsDurationPath(), $this->meetingsDuration, 'MeetingsDuration');
    }

    /**
     * @throws OptimiseException
     */
    private function writeMeetingsAvailability()
    {
        self::writeCSVMatrix($this->getMeetingsAvailabilityPath(), $this->meetingsAvailability, 'MeetingsAvailability');
    }

    /**
     * @throws OptimiseException
     */
    private function writeUsersAvailability()
    {
        self::writeCSVMatrix($this->getUsersAvailabilityPath(), $this->usersAvailability, 'UsersAvailability');
    }

    /**
     * @throws OptimiseException
     */
    private function writeUsersMeetings()
    {
        self::writeCSVMatrix($this->getUsersMeetingsPath(), $this->usersMeetings, 'UsersMeetings');
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

        //fputcsv($fp, []); //empty line

        fclose($fp);
    }

    /**
     * @throws OptimiseException
     */
    public function solve()
    {
        $this->writeData();
        $this->writeModelFile();
        $event = $this->schedule->exec('glpsol --math '.$this->getModelPath())->sendOutputTo($this->getOutputPath())->after(function () { }); //this just to execute in foreground
        if($event->isDue($this->laravel))
            $event->run($this->laravel);
        //TODO catch glpsol errors
    }

    /**
     * @throws OptimiseException
     */
    private function writeModelFile()
    {
        $strReplaceS = array('{USERS_PATH}', '{MEETINGS_PATH}', '{USER_AVAILABILITY_PATH}', '{MEETINGS_AVAILABILITY_PATH}', '{USER_MEETINGS_PATH}', '{MEETINGS_DURATION_PATH}', '{TIME_SLOTS}', '{MAX_TIME_SLOTS}', '{X_OUT_PATH}', '{Y_OUT_PATH}');
        $strReplaceR = array($this->getUsersPath(), $this->getMeetingsPath(), $this->getUsersAvailabilityPath(), $this->getMeetingsAvailabilityPath(), $this->getUsersMeetingsPath(), $this->getMeetingsDurationPath(), $this->timeSlots, $this->maxTimeSlots, $this->getXPath(), $this->getYPath());
        $f = @fopen($this->getModelPath(), "w");
        if(!$f)
            throw new OptimiseException('problem during creation of a file');
        fwrite($f, str_replace($strReplaceS, $strReplaceR, file_get_contents(__DIR__ . "/model.stub")));
        fclose($f);
    }

    /**
     * @return array
     * @throws OptimiseException
     */
    public function getXResults()
    {
        return self::readCSVFile($this->getXPath());
    }

    /**
     * @return array
     * @throws OptimiseException
     */
    public function getYResults()
    {
        return self::readCSVFile($this->getYPath());
    }

    /**
     * @return string
     * @throws OptimiseException
     */
    public function getOutput()
    {
        $handle = @fopen($this->getOutputPath(),"r");
        if(!$handle)
            throw new OptimiseException('problems during reading the file');
        fclose($handle);
        return file_get_contents($this->getOutputPath());
    }

    /**
     * @param string $file
     * @return array
     * @throws OptimiseException
     */
    static private function readCSVFile($file)
    {
        if(!file_exists($file) || !filesize($file))
            throw new OptimiseException('no results file');

        $handle = @fopen($file,"r");
        if(!$handle)
            throw new OptimiseException('problems during reading the file');

        $ret = [];
        fgetcsv($handle); //skip head
        while (($data = fgetcsv($handle)) !== FALSE) {
            if(count($data) != 3) {
                fclose($handle);
                throw new OptimiseException('problems during parsing the file');
            }

            $ret[$data[0]][$data[1]] = $data[2];
        }

        fclose($handle);

        return $ret;
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
     * @return string
     */
    private function getXPath()
    {
        return $this->path.'/x.csv';
    }

    /**
     * @return string
     */
    private function getYPath()
    {
        return $this->path.'/y.csv';
    }

    /**
     * @return string
     */
    private function getOutputPath()
    {
        return $this->path.'/out.txt';
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
        $this->checkArrayProprieties(['users', 'meetings', 'meetingsAvailability', 'meetingsDuration', 'usersAvailability', 'usersMeetings']);
        $this->checkIntProprieties(['timeSlots', 'maxTimeSlots']);
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

    /**
     * implementation of arraypad that doesn't change original keys<br/>
     * <strong>CAUTION: Only positive $len</strong>
     * @param array $array
     * @return array
     */
    static private function arrayPad(array $array, $len, $pad)
    {
        $len = $len - count($array);
        for($i = 0; $i<$len; $i++)
            $array[] = $pad;
        return $array;
    }
}