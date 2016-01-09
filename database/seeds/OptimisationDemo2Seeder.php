<?php

use Illuminate\Database\Seeder;

class OptimisationDemo2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        self::complexModel();
        self::complexModel2();
    }

    static private function  complexModel()
    {
        $company = factory(\plunner\Company::class)->create(['password' => bcrypt('test')]);
        $employees = factory(\plunner\Employee::class, 5)->make(['password' => bcrypt('test')])->each(function ($employee) use($company){
            $company->employees()->save($employee);
            $employee->calendars()->save(factory(\plunner\Calendar::class)->make(['enabled'=>true]));
        });

        $group1 = factory(\plunner\Group::class)->make();
        $group2 = factory(\plunner\Group::class)->make();
        $group3 = factory(\plunner\Group::class)->make();
        $company->groups()->save($group1);
        $company->groups()->save($group2);
        $company->groups()->save($group3);
        $group1->employees()->attach([$employees[0]->id, $employees[1]->id, $employees[2]->id]);
        $group2->employees()->attach([$employees[1]->id, $employees[2]->id, $employees[3]->id]);
        $group3->employees()->attach([$employees[3]->id, $employees[4]->id]);

        $meeting1 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting1);
        $meeting2 = factory(\plunner\Meeting::class)->make(['duration' => 1* config('app.timeslots.duration')]);
        $group2->meetings()->save($meeting2);
        $meeting3 = factory(\plunner\Meeting::class)->make(['duration' => 1* config('app.timeslots.duration')]);
        $group3->meetings()->save($meeting3);

        $now = (new \DateTime())->modify('next monday');
        $timeslots1 = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now)];
        $timeslots2 = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now, 3)];
        $timeslots3 = ['time_start' => self::addTimeInterval(clone $now, 3), 'time_end' => self::addTimeInterval(clone $now, 4)];
        $meeting1->timeslots()->create($timeslots1);
        $meeting3->timeslots()->create($timeslots2);
        $meeting2->timeslots()->create($timeslots1);
        $meeting2->timeslots()->create($timeslots3);
        /*$timeslotsAll = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now, 4)];
        $employees->each(function($employee) use ($timeslotsAll){
            $employee->calendars()->first()->timeslots()->create($timeslotsAll);
        });*/

        print_r($company->toArray());
        print_r($employees->toArray());
    }

    /**
     * @param \DateTime $date
     * @param Int $multiplier
     * @return \DateTime
     */
    static private function addTimeInterval(\DateTime $date, $multiplier=1)
    {
        return $date->add(new \DateInterval('PT' . config('app.timeslots.duration') * $multiplier . 'S'));
    }

    static private function complexModel2()
    {
        $company = factory(\plunner\Company::class)->create(['password' => bcrypt('test')]);
        $employees = factory(\plunner\Employee::class, 3)->make(['password' => bcrypt('test')])->each(function ($employee) use ($company) {
            $company->employees()->save($employee);
            $employee->calendars()->save(factory(\plunner\Calendar::class)->make(['enabled' => true]));
        });

        $group1 = factory(\plunner\Group::class)->make();
        $group2 = factory(\plunner\Group::class)->make();
        $group3 = factory(\plunner\Group::class)->make();
        $company->groups()->save($group1);
        $company->groups()->save($group2);
        $company->groups()->save($group3);
        $group1->employees()->attach([$employees[0]->id, $employees[1]->id, $employees[2]->id]);
        $group2->employees()->attach([$employees[1]->id, $employees[2]->id]);
        $group3->employees()->attach([$employees[2]->id]);

        $meeting1 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting1);
        $meeting2 = factory(\plunner\Meeting::class)->make(['duration' => 2 * config('app.timeslots.duration')]);
        $group2->meetings()->save($meeting2);
        $meeting3 = factory(\plunner\Meeting::class)->make(['duration' => 3 * config('app.timeslots.duration')]);
        $group3->meetings()->save($meeting3);

        $now = (new \DateTime())->modify('next monday');
        $timeslots1 = ['time_start' => clone $now, 'time_end' => self::addTimeInterval(clone $now, 4)];
        $meeting1->timeslots()->create($timeslots1);
        $meeting3->timeslots()->create($timeslots1);
        $meeting2->timeslots()->create($timeslots1);
        $timeslotsE1 = ['time_start' => clone $now, 'time_end' => self::addTimeInterval(clone $now, 1)];
        $timeslotsE2 = ['time_start' => self::addTimeInterval(clone $now, 3), 'time_end' => self::addTimeInterval(clone $now, 4)];
        $employees[0]->calendars()->first()->timeslots()->create($timeslotsE1);
        $employees[1]->calendars()->first()->timeslots()->create($timeslotsE2);
        $employees[2]->calendars()->first()->timeslots()->create($timeslotsE2);

        print_r($company->toArray());
        print_r($employees->toArray());
    }
}
