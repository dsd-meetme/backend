<?php
/**
 * Created by PhpStorm.
 * User: Claudio Cardinale <cardi@thecsea.it>
 * Date: 18/12/15
 * Time: 15.20
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

/**
 * Class Path
 * tmp path where csv files for the solver task
 * @package plunner\Console\Commands\Optimise
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class Path
{
    /**
     * @var string
     */
    private $path;

    /**
     * Path constructor.
     * @param string $path
     */
    private function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @throws OptimiseException
     */
    public function __destruct()
    {
        if ($this->path && is_dir($this->path) && !self::delTree($this->path))
            throw new OptimiseException('problems during removing of path directory');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


    /**
     * @throws OptimiseException on problems during creation of tmp dir
     */
    static public function createPath()
    {
        $path = new Path(tempnam(sys_get_temp_dir(), 'OPT')); //TODO check the return in case of errors this return false on failure
        unlink($path->getPath()); //remove file to create a dir
        if(file_exists($path->getPath()))
            throw new OptimiseException('problem during creation of tmp dir (the directory already exists)');
        if(!@mkdir($path->getPath()))
            throw new OptimiseException('problem during creation of tmp dir (mkdir problem)');;
        if(! is_dir($path->getPath()))
            throw new OptimiseException('problem during creation of tmp dir (it is not possible to create directory)');
        return $path;
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
     * @return string
     */
    public function getModelPath()
    {
        return $this->path.'/model.mod';
    }

    /**
     * @return string
     */
    public function getUsersPath()
    {
        return $this->path.'/Users.csv';
    }

    /**
     * @return string
     */
    public function getMeetingsPath()
    {
        return $this->path.'/Meeting.csv';
    }

    /**
     * @return string
     */
    public function getMeetingsDurationPath()
    {
        return $this->path.'/MeetingsDuration.csv';
    }

    /**
     * @return string
     */
    public function getMeetingsAvailabilityPath()
    {
        return $this->path.'/MeetingsAvailability.csv';
    }

    /**
     * @return string
     */
    public function getUsersAvailabilityPath()
    {
        return $this->path.'/UsersAvailability.csv';
    }

    /**
     * @return string
     */
    public function getUsersMeetingsPath()
    {
        return $this->path.'/UsersMeetings.csv';
    }

    /**
     * @return string
     */
    public function getXPath()
    {
        return $this->path.'/x.csv';
    }

    /**
     * @return string
     */
    public function getYPath()
    {
        return $this->path.'/y.csv';
    }

    /**
     * @return string
     */
    public function getOutputPath()
    {
        return $this->path.'/out.txt';
    }
}