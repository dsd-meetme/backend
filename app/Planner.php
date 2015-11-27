<?php

namespace plunner;


/**
 * Class Planner
 * @package plunner
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class Planner extends Employee
{

    public function groupsManaged()
    {
        return $this->HasMany(Group::class);
    }
}
