<?php
/**
 * Created by PhpStorm.
 * User: Claudio Cardinale <cardi@thecsea.it>
 * Date: 03/12/15
 * Time: 2.37
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

/**
 * Class Autoloader
 * @package plunner\Console\Commands\SyncCaldav
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class Autoloader extends \Worker {

    public function __construct($loader) {
        $this->loader = $loader;
    }

    /* include autoloader for Tasks */
    public function run()   { require_once($this->loader); }

    /* override default inheritance behaviour for the new threaded context */
    public function start($options =  null) { return parent::start(PTHREADS_INHERIT_NONE); }

    protected $loader;
}