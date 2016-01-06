<?php
/**
 * Created by PhpStorm.
 * User: Claudio Cardinale <cardi@thecsea.it>
 * Date: 07/12/15
 * Time: 21.24
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


use Exception;

class OptimiseException extends \Exception
{
    /**
     * @var bool
     */
    private $empty = false;

    /**
     * OptimiseException constructor.
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @param bool $empty
     */
    public function __construct($message =  "", $code = 0, Exception $previous = null, $empty = false)
    {
        parent::__construct($message, $code, $previous);
        $this->empty = $empty;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->empty;
    }

    /**
     * @param boolean $empty
     */
    public function setEmpty($empty)
    {
        $this->empty = $empty;
    }

    /**
     * @param boolean $empty
     * @return OptimiseException
     */
    public function withEmpty($empty)
    {
        $this->empty = $empty;
        return $this;
    }
}