<?php

use Illuminate\Database\Seeder;

class OptimisationDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        self::simpleModel();

    }

    static private function  simpleModel()
    {
        $company = factory(\plunner\Company::class)->create(['password' => bcrypt('test')]);
        $employees = factory(\plunner\Employee::class, 3)->make(['password' => bcrypt('test')])->each(function ($employee) use($company){
            $company->employees()->save($employee);
            $employee->calendars()->save(factory(\plunner\Calendar::class)->make(['enabled'=>true]));
        });

        $group1 = factory(\plunner\Group::class)->make();
        $group2 = factory(\plunner\Group::class)->make();
        $company->groups()->save($group1);
        $company->groups()->save($group2);
        $group1->employees()->attach($employees->pluck('id')->toArray());
        $employeeNo = $employees->pop();
        $group2->employees()->attach($employees->pluck('id')->toArray());

        $meeting1 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting1);
        $meeting2 = factory(\plunner\Meeting::class)->make(['duration' => 3 * config('app.timeslots.duration')]);
        $group2->meetings()->save($meeting2);

        $now = (new \DateTime())->modify('next monday');
        $timeslots1 = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now)];
        $timeslots2 = ['time_start' => clone $now, 'time_end'=>self::addTimeInterval(clone $now, 3)];
        $meeting1->timeslots()->create($timeslots1);
        $meeting2->timeslots()->create($timeslots2);
        $timeslotsE = ['time_start' => self::addTimeInterval(clone $now, 3), 'time_end' => self::addTimeInterval(
            clone $now, config('app.timeslots.number'))];
        $timeslotsENo = ['time_start' => self::addTimeInterval(clone $now, 1), 'time_end' => self::addTimeInterval(
            clone $now, config('app.timeslots.number'))];
        $employees->each(function($employee) use ($timeslotsE){
            $employee->calendars()->first()->timeslots()->create($timeslotsE);
        });
        $employeeNo->calendars()->first()->timeslots()->create($timeslotsENo);

        print_r($company->toArray());
        print_r($employees->toArray());
        print_r($employeeNo->toArray());
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
}
