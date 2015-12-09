<?php

namespace plunner\Policies;

use plunner\Calendar;
use plunner\PolicyCheckable;

/**
 * Class CalendarPolicy
 * @package plunner\Policies
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class CalendarPolicy
{
    /**
     * Create a new policy instance.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Calendar $calendar
     * @return bool
     */
    public function index(PolicyCheckable $policyCheckable, Calendar $calendar)
    {
        return $this->userCheck($policyCheckable, $calendar);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Calendar $calendar
     * @return bool
     */
    public function store(PolicyCheckable $policyCheckable, Calendar $calendar)
    {
        return $this->userCheck($policyCheckable, $calendar);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Calendar $calendar
     * @return bool
     */
    public function update(PolicyCheckable $policyCheckable, Calendar $calendar)
    {
        return $this->userCheck($policyCheckable, $calendar);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Calendar $calendar
     * @return bool
     */
    public function show(PolicyCheckable $policyCheckable, Calendar $calendar)
    {
        $ret = $this->userCheck($policyCheckable, $calendar);
        return $ret;
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Calendar $calendar
     * @return bool
     */
    public function destroy(PolicyCheckable $policyCheckable, Calendar $calendar)
    {
        return $this->userCheck($policyCheckable, $calendar);
    }

    /**
     * @param PolicyCheckable $policyCheckable
     * @param Calendar $calendar
     * @return bool
     */
    private function userCheck(PolicyCheckable $policyCheckable, Calendar $calendar)
    {
        return $policyCheckable->verifyCalendar($calendar);
    }
}
