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
use Illuminate\Foundation\Application;

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
     * @var Path
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

    /**
     * @var Schedule laravel schedule object needed to perform command in background
     */
    private $schedule;

    /**
     * @var Application
     */
    private $laravel;

    //TODo clone function rmemeber to clone also path or create a new one
    //TODO mehtod to check if all variables are correctly set
    //TODO check no duplicates
    //TODO exception if glpsol return erros
    //TODO intercept all erros of system calls like mkdir
    //TODO to_string

    /**
     * Solver constructor.
     * @param Schedule $schedule
     * @param Application $laravel
     * @throws OptimiseException on general problems
     */
    public function __construct(Schedule $schedule, Application $laravel)
    {
        self::checkGlpsol();
        $this->path = Path::createPath();
        $this->schedule = $schedule;
        $this->laravel = $laravel;
    }

    /**
     * @throws OptimiseException
     */
    static private function checkGlpsol()
    {
        if (!(`which glpsol`))
            throw new OptimiseException('glpsol is not installed');
    }

    /**
     * @throws OptimiseException
     */
    public function __destruct()
    {
        $this->path = null; //call the path destruct
    }

    /**
     * @return Path
     */
    public function getPath()
    {
        return clone $this->path;
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
     * @return Solver
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param string[] $users
     * @return Solver
     */
    public function setUsers($users)
    {
        $this->users = $users;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getMeetings()
    {
        return $this->meetings;
    }

    /**
     * @param string[] $meetings
     * @return Solver
     */
    public function setMeetings($meetings)
    {
        $this->meetings = $meetings;
        return $this;
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
     * @return Solver
     * @throws OptimiseException
     */
    public function setTimeSlots($timeSlots)
    {
        if (!is_int($timeSlots) || $timeSlots <= 0)
            throw new OptimiseException('$timeSlots is not integer or it is not >0');

        $this->timeSlots = $timeSlots;
        return $this;
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
     * @return Solver
     * @throws OptimiseException
     */
    public function setMaxTimeSlots($maxTimeSlots)
    {
        if (!is_int($maxTimeSlots) || $maxTimeSlots <= 0)
            throw new OptimiseException('$maxTimeSlots is not integer or it is not >0');

        $this->maxTimeSlots = $maxTimeSlots;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getMeetingsAvailability()
    {
        return $this->meetingsAvailability;
    }

    /**
     * @param string[] $meetingsAvailability
     * @return Solver
     * @throws OptimiseException
     */
    public function setMeetingsAvailability($meetingsAvailability)
    {
        $meetings = array_keys($meetingsAvailability);
        if (array_diff($meetings, $this->meetings))
            throw new OptimiseException('meetings different from meetings set');
        foreach ($meetingsAvailability as $key => $meetingsAvailabilityS) {
            $timeSlots = array_keys($meetingsAvailabilityS);//TODO this is useless, we can use directly $usersAvailabilityS
            if (count($timeSlots) != $this->timeSlots)
                throw new OptimiseException('timeSlots different from timeSlots set');
            $meetingsAvailability[$key] = self::arrayPad($meetingsAvailabilityS, $this->timeSlots + $this->maxTimeSlots, 0);
        }

        $this->meetingsAvailability = $meetingsAvailability;
        return $this;
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
        for ($i = 0; $i < $len; $i++)
            $array[] = $pad;
        return $array;
    }

    /**
     * @return string[]
     */
    public function getMeetingsDuration()
    {
        return $this->meetingsDuration;
    }

    /**
     * @param string[] $meetingsDuration
     * @return Solver
     * @throws OptimiseException
     */
    public function setMeetingsDuration($meetingsDuration)
    {
        $meetings = array_keys($meetingsDuration);
        if (array_diff($meetings, $this->meetings)) {
            print "";
            throw new OptimiseException('meetings different from meetings set');
        }
        foreach ($meetingsDuration as $duration) {
            $duration = (int)$duration; //TODO fix this (fix for optimise)
            if (!is_int($duration) || $duration <= 0)
                throw new OptimiseException('duration is not integer or it is not >0');
        }

        $this->meetingsDuration = $meetingsDuration;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getUsersAvailability()
    {
        return $this->usersAvailability;
    }

    /**
     * @param string[] $usersAvailability
     * @return Solver
     * @throws OptimiseException
     */
    public function setUsersAvailability($usersAvailability)
    {
        $users = array_keys($usersAvailability);
        if (array_diff($users, $this->users))
            throw new OptimiseException('users different from users set');
        foreach ($usersAvailability as $key => $usersAvailabilityS) {
            $timeSlots = array_keys($usersAvailabilityS);//TODO this is useless, we can use directly $usersAvailabilityS
            if (count($timeSlots) != $this->timeSlots)
                throw new OptimiseException('timeSlots different from timeSlots set');

            $usersAvailability[$key] = self::arrayPad($usersAvailabilityS, $this->timeSlots + $this->maxTimeSlots, 0);
        }

        $this->usersAvailability = $usersAvailability;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getUsersMeetings()
    {
        return $this->usersMeetings;
    }

    /**
     * @param string[] $usersMeetings
     * @return Solver
     * @throws OptimiseException
     */
    public function setUsersMeetings($usersMeetings)
    {
        $users = array_keys($usersMeetings);
        if (array_diff($users, $this->users))
            throw new OptimiseException('users different from users set');
        foreach ($usersMeetings as $usersMeetingsS) {
            $meetings = array_keys($usersMeetingsS);
            if (array_diff($meetings, $this->meetings))
                throw new OptimiseException('meetings different from meetings set');
        }

        $this->usersMeetings = $usersMeetings;
        return $this;
    }

    /**
     * @return Solver
     * @throws OptimiseException
     */
    public function solve()
    {
        $this->writeData();
        $this->writeModelFile();
        $event = $this->schedule->exec('glpsol --math ' . $this->path->getModelPath())->sendOutputTo($this->path->getOutputPath())->after(function () {
        }); //this just to execute in foreground
        if ($event->isDue($this->laravel))
            $event->run($this->laravel);
        //TODO catch glpsol errors
        return $this;
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
        foreach ($proprieties as $propriety)
            if (count($this->$propriety) == 0)
                throw new OptimiseException($propriety . ' property is not set correctly');
    }

    /**
     * @param $proprieties
     * @throws OptimiseException
     */
    private function checkIntProprieties($proprieties)
    {
        foreach ($proprieties as $propriety)
            if (!is_int($this->$propriety) || $this->$propriety <= 0)
                throw new OptimiseException($propriety . ' property is not set correctly');
    }

    /**
     * @throws OptimiseException
     */
    private function writeUsers()
    {
        self::writeCSVArrayNoKey($this->path->getUsersPath(), $this->users);
    }

    /**
     * @param string $file
     * @param array $data
     * @throws OptimiseException
     */
    static private function writeCSVArrayNoKey($file, $data)
    {
        $f = function ($fp, $data) {
            foreach ($data as $field) {
                fputcsv($fp, [$field]);
            }
        };

        self::writeCSV($file, $data, ['i'], $f);
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
        if (!$fp)
            throw new OptimiseException('problem during creation of a file');

        fputcsv($fp, $heading);

        $writer($fp, $data);

        //fputcsv($fp, []); //empty line

        fclose($fp);
    }

    /**
     * @throws OptimiseException
     */
    private function writeMeetings()
    {
        self::writeCSVArrayNoKey($this->path->getMeetingsPath(), $this->meetings);
    }

    /**
     * @throws OptimiseException
     */
    private function writeMeetingsDuration()
    {
        self::writeCSVArray($this->path->getMeetingsDurationPath(), $this->meetingsDuration, 'MeetingsDuration');
    }

    /**
     * @param string $file
     * @param array $data
     * @param string $name
     * @throws OptimiseException
     */
    static private function writeCSVArray($file, $data, $name)
    {
        $f = function ($fp, $data) {
            foreach ($data as $key => $field) {
                fputcsv($fp, [$key, $field]);
            }
        };

        self::writeCSV($file, $data, ['i', $name], $f);
    }

    /**
     * @throws OptimiseException
     */
    private function writeMeetingsAvailability()
    {
        self::writeCSVMatrix($this->path->getMeetingsAvailabilityPath(), $this->meetingsAvailability, 'MeetingsAvailability');
    }

    /**
     * @param string $file
     * @param array $data
     * @param string $name
     * @throws OptimiseException
     */
    static private function writeCSVMatrix($file, $data, $name)
    {
        $f = function ($fp, $data) {
            foreach ($data as $key => $field) {
                foreach ($field as $key2 => $field2)
                    fputcsv($fp, [$key, $key2, $field2]);
            }
        };

        self::writeCSV($file, $data, ['i', 'j', $name], $f);
    }

    /**
     * @throws OptimiseException
     */
    private function writeUsersAvailability()
    {
        self::writeCSVMatrix($this->path->getUsersAvailabilityPath(), $this->usersAvailability, 'UsersAvailability');
    }

    /**
     * @throws OptimiseException
     */
    private function writeUsersMeetings()
    {
        self::writeCSVMatrix($this->path->getUsersMeetingsPath(), $this->usersMeetings, 'UsersMeetings');
    }

    /**
     * @throws OptimiseException
     */
    private function writeModelFile()
    {
        $strReplaceS = array('{USERS_PATH}', '{MEETINGS_PATH}', '{USER_AVAILABILITY_PATH}', '{MEETINGS_AVAILABILITY_PATH}', '{USER_MEETINGS_PATH}', '{MEETINGS_DURATION_PATH}', '{TIME_SLOTS}', '{MAX_TIME_SLOTS}', '{X_OUT_PATH}', '{Y_OUT_PATH}');
        $strReplaceR = array($this->path->getUsersPath(), $this->path->getMeetingsPath(), $this->path->getUsersAvailabilityPath(), $this->path->getMeetingsAvailabilityPath(), $this->path->getUsersMeetingsPath(), $this->path->getMeetingsDurationPath(), $this->timeSlots, $this->maxTimeSlots, $this->path->getXPath(), $this->path->getYPath());
        $f = @fopen($this->path->getModelPath(), "w");
        if (!$f)
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
        return self::readCSVFile($this->path->getXPath());
    }

    /**
     * @param string $file
     * @return array
     * @throws OptimiseException
     */
    static private function readCSVFile($file)
    {
        if (!file_exists($file) || !filesize($file))
            throw new OptimiseException('no results file');

        $handle = @fopen($file, "r");
        if (!$handle)
            throw new OptimiseException('problems during reading the file');

        $ret = [];
        fgetcsv($handle); //skip head
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (count($data) != 3) {
                fclose($handle);
                throw new OptimiseException('problems during parsing the file');
            }

            $ret[$data[0]][$data[1]] = $data[2];
        }

        fclose($handle);

        return $ret;
    }

    /**
     * @return array
     * @throws OptimiseException
     */
    public function getYResults()
    {
        return self::readCSVFile($this->path->getYPath());
    }

    /**
     * @return string
     * @throws OptimiseException
     */
    public function getOutput()
    {
        if (!($data = file_get_contents($this->path->getOutputPath())))
            throw new OptimiseException('problems during reading the file');
        return $data;
    }
}