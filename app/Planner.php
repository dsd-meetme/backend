<?php

namespace plunner;


class Planner extends Employee
{

    public function groupsManaged()
    {
        return $this->HasMany(Group::class);
    }
}
