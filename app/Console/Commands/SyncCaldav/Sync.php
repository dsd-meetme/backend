<?php
/**
 * Created by PhpStorm.
 * User: Claudio Cardinale <cardi@thecsea.it>
 * Date: 03/12/15
 * Time: 2.33
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

namespace plunner\Console\Commands\SyncCaldav;

use it\thecsea\caldav_client_adapter\simple_caldav_client\SimpleCaldavAdapter;
use \it\thecsea\caldav_client_adapter\EventInterface;
use plunner\Caldav;
use plunner\Events\CaldavErrorEvent;

/**
 * Class Sync
 * @package plunner\Console\Commands
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class Sync
{
    /**
     * @var Caldav
     */
    private $calendar;

    /**
     * Sync constructor.
     * @param Caldav $calendar
     */
    public function __construct(Caldav $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @return Caldav
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * perform the sync
     */
    public function sync()
    {
        $this->syncToTimeSlots();
    }

    /**
     * @return array|\it\thecsea\caldav_client_adapter\EventInterface[]
     * @throws \it\thecsea\caldav_client_adapter\CaldavException
     * @thorws \Illuminate\Contracts\Encryption\DecryptException
     */
    private function getEvents()
    {
        $caldavClient = new SimpleCaldavAdapter();
        $caldavClient->connect($this->calendar->url, $this->calendar->username, \Crypt::decrypt($this->calendar->password));
        $calendars = $caldavClient->findCalendars();
        $caldavClient->setCalendar($calendars[$this->calendar->calendar_name]);
        /**
         * 26 hours before to avoid tiemezone problems and dst problems
         * 30 days after
         */
        return $caldavClient->getEvents(date('Ymd\THis\Z', time()-93600), date('Ymd\THis\Z', time()+2592000));
    }

    /**
     *
     */
    private function syncToTimeSlots()
    {
        try
        {
            $events = $this->getEvents();
        }catch (\it\thecsea\caldav_client_adapter\CaldavException $e)
        {
            \Event::fire(new CaldavErrorEvent($this->calendar, $e->getMessage()));
        }catch(\Illuminate\Contracts\Encryption\DecryptException $e){
            \Event::fire(new CaldavErrorEvent($this->calendar, $e->getMessage()));
        }

        /**
         * @var $calendarMain \plunner\Calendar
         */
        $calendarMain = $this->calendar->calendar;

        //remove old timeslots
        $calendarMain->timeslots()->delete();
        foreach($events as $event){
            if(!($event = $this->parseEvent($event)))
                \Event::fire(new CaldavErrorEvent($this->calendar, 'problem during the parsing an event'));
            $calendarMain->timeslots()->create($event);
        }
    }

    /**
     * @param EventInterface $event
     * @return \DateTime[]|null
     */
    private function parseEvent(EventInterface $event)
    {
        $pattern = "/^((DTSTART;)|(DTEND;))(.*)\$/m";
        if(preg_match_all($pattern, $event->getData(), $matches)){
            if(!isset($matches[4]) || count($matches[4]) != 2)
                return null;
            $ret = [];
            if($tmp = $this->parseDate($matches[4][0]))
                $ret['time_start'] = $tmp;
            else
                return null;
            if($tmp = $this->parseDate($matches[4][1]))
                $ret['time_end'] = $tmp;
            else
                return null;
            return $ret;
        }
        return null;
    }

    /**
     * @param String $date
     * @return \DateTime|null|false
     */
    private function parseDate($date)
    {
        $pattern = "/^((TZID=)|(VALUE=))(.*):(.*)\$/m";
        if(preg_match_all($pattern, $date, $matches)){
            if($matches[1][0] == 'TZID=')
            {
                return \DateTime::createFromFormat('Ymd\THis', $matches[5][0], new \DateTimeZone($matches[4][0]));
            }else if($matches[1][0] == 'VALUE=' && $matches[4][0] == 'DATE')
            {
                return \DateTime::createFromFormat('Ymd\THis', $matches[5][0].'T000000');
            }
        }
        return null;
    }
}