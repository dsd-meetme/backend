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
        self::complexModel3();
        self::complexModel4();
        self::complexModel5();
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

        print "1\n";
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

        print "2\n";
        print_r($company->toArray());
        print_r($employees->toArray());
    }

    static private function complexModel3()
    {
        $company = factory(\plunner\Company::class)->create(['password' => bcrypt('test')]);
        $employees = factory(\plunner\Employee::class, 4)->make(['password' => bcrypt('test')])->each(function ($employee) use ($company) {
            $company->employees()->save($employee);
            $employee->calendars()->save(factory(\plunner\Calendar::class)->make(['enabled' => true]));
        });

        $group1 = factory(\plunner\Group::class)->make();
        $company->groups()->save($group1);
        $group1->employees()->attach([$employees[0]->id, $employees[1]->id, $employees[2]->id, $employees[3]->id]);


        $meeting1 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting1);
        $meeting2 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting2);

        $now = (new \DateTime())->modify('next monday');
        $timeslots1 = ['time_start' => clone $now, 'time_end' => self::addTimeInterval(clone $now, 1)];
        $timeslots2 = ['time_start' => self::addTimeInterval(clone $now, 2), 'time_end' => self::addTimeInterval(clone $now, 3)];
        $timeslots3 = ['time_start' => self::addTimeInterval(clone $now, 4), 'time_end' => self::addTimeInterval(clone $now, 5)];
        $timeslots4 = ['time_start' => self::addTimeInterval(clone $now, 96 + 1), 'time_end' => self::addTimeInterval(clone $now, 96 + 2)];
        $timeslots5 = ['time_start' => self::addTimeInterval(clone $now, 96 + 3), 'time_end' => self::addTimeInterval(clone $now, 96 + 4)];
        $timeslots6 = ['time_start' => self::addTimeInterval(clone $now, 96 * 2 + 2), 'time_end' => self::addTimeInterval(clone $now, 96 * 2 + 3)];
        $timeslots7 = ['time_start' => self::addTimeInterval(clone $now, 96 * 2 + 4), 'time_end' => self::addTimeInterval(clone $now, 96 * 2 + 5)];
        $meeting1->timeslots()->create($timeslots1);
        $meeting1->timeslots()->create($timeslots3);
        $meeting1->timeslots()->create($timeslots4);
        $meeting1->timeslots()->create($timeslots7);
        $meeting2->timeslots()->create($timeslots2);
        $meeting2->timeslots()->create($timeslots5);
        $meeting2->timeslots()->create($timeslots6);
        $employees[0]->calendars()->first()->timeslots()->create($timeslots1);
        $employees[1]->calendars()->first()->timeslots()->create($timeslots5);
        $employees[2]->calendars()->first()->timeslots()->create($timeslots6);
        $employees[3]->calendars()->first()->timeslots()->create($timeslots7);

        print "3\n";
        print_r($company->toArray());
        print_r($employees->toArray());
    }

    static private function complexModel4()
    {
        $company = factory(\plunner\Company::class)->create(['password' => bcrypt('test')]);
        $employees = factory(\plunner\Employee::class, 4)->make(['password' => bcrypt('test')])->each(function ($employee) use ($company) {
            $company->employees()->save($employee);
            $employee->calendars()->save(factory(\plunner\Calendar::class)->make(['enabled' => true]));
        });

        $group1 = factory(\plunner\Group::class)->make();
        $company->groups()->save($group1);
        $group1->employees()->attach([$employees[0]->id, $employees[1]->id, $employees[2]->id, $employees[3]->id]);


        $meeting1 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting1);
        $meeting2 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting2);

        $now = (new \DateTime())->modify('next monday');
        $timeslots1 = ['time_start' => clone $now, 'time_end' => self::addTimeInterval(clone $now, 1)];
        $timeslots2 = ['time_start' => self::addTimeInterval(clone $now, 2), 'time_end' => self::addTimeInterval(clone $now, 3)];
        $timeslots3 = ['time_start' => self::addTimeInterval(clone $now, 96 + 1), 'time_end' => self::addTimeInterval(clone $now, 96 + 2)];
        $timeslots4 = ['time_start' => self::addTimeInterval(clone $now, 96 + 2), 'time_end' => self::addTimeInterval(clone $now, 96 + 3)];
        $timeslots5 = ['time_start' => self::addTimeInterval(clone $now, 96 * 2 + 1), 'time_end' => self::addTimeInterval(clone $now, 96 * 2 + 2)];
        $timeslots6 = ['time_start' => self::addTimeInterval(clone $now, 96 * 2 + 3), 'time_end' => self::addTimeInterval(clone $now, 96 * 2 + 4)];
        $meeting1->timeslots()->create($timeslots1);
        $meeting1->timeslots()->create($timeslots4);
        $meeting1->timeslots()->create($timeslots5);
        $meeting2->timeslots()->create($timeslots2);
        $meeting2->timeslots()->create($timeslots3);
        $meeting2->timeslots()->create($timeslots6);
        $employees[0]->calendars()->first()->timeslots()->create($timeslots2);
        $employees[0]->calendars()->first()->timeslots()->create($timeslots3);
        $employees[0]->calendars()->first()->timeslots()->create($timeslots6);
        $employees[1]->calendars()->first()->timeslots()->create($timeslots2);
        //$employees[2]->calendars()->first()->timeslots()->create($timeslots3);
        $employees[3]->calendars()->first()->timeslots()->create($timeslots6);

        print "4\n";
        print_r($company->toArray());
        print_r($employees->toArray());
    }

    static private function complexModel5()
    {
        $company = factory(\plunner\Company::class)->create(['password' => bcrypt('test')]);
        $employees = factory(\plunner\Employee::class, 4)->make(['password' => bcrypt('test')])->each(function ($employee) use ($company) {
            $company->employees()->save($employee);
            $employee->calendars()->save(factory(\plunner\Calendar::class)->make(['enabled' => true]));
        });

        $group1 = factory(\plunner\Group::class)->make();
        $company->groups()->save($group1);
        $group1->employees()->attach([$employees[0]->id, $employees[1]->id, $employees[2]->id, $employees[3]->id]);


        $meeting1 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting1);
        $meeting2 = factory(\plunner\Meeting::class)->make(['duration' => 1 * config('app.timeslots.duration')]);
        $group1->meetings()->save($meeting2);

        $now = (new \DateTime())->modify('next monday');
        $timeslots1 = ['time_start' => clone $now, 'time_end' => self::addTimeInterval(clone $now, 1)];
        $timeslots2 = ['time_start' => self::addTimeInterval(clone $now, 2), 'time_end' => self::addTimeInterval(clone $now, 3)];
        $timeslots3 = ['time_start' => self::addTimeInterval(clone $now, 96 + 1), 'time_end' => self::addTimeInterval(clone $now, 96 + 2)];
        $timeslots4 = ['time_start' => self::addTimeInterval(clone $now, 96 + 2), 'time_end' => self::addTimeInterval(clone $now, 96 + 3)];
        $timeslots5 = ['time_start' => self::addTimeInterval(clone $now, 96 * 2 + 1), 'time_end' => self::addTimeInterval(clone $now, 96 * 2 + 2)];
        $timeslots6 = ['time_start' => self::addTimeInterval(clone $now, 96 * 2 + 3), 'time_end' => self::addTimeInterval(clone $now, 96 * 2 + 4)];
        $meeting1->timeslots()->create($timeslots1);
        $meeting1->timeslots()->create($timeslots4);
        $meeting1->timeslots()->create($timeslots5);
        $meeting2->timeslots()->create($timeslots2);
        $meeting2->timeslots()->create($timeslots3);
        $meeting2->timeslots()->create($timeslots6);
        $meeting2->timeslots()->create($timeslots1);
        $employees[0]->calendars()->first()->timeslots()->create($timeslots2);
        $employees[0]->calendars()->first()->timeslots()->create($timeslots3);
        $employees[0]->calendars()->first()->timeslots()->create($timeslots6);
        $employees[1]->calendars()->first()->timeslots()->create($timeslots2);
        //$employees[2]->calendars()->first()->timeslots()->create($timeslots3);
        $employees[3]->calendars()->first()->timeslots()->create($timeslots6);

        print "5\n";
        print_r($company->toArray());
        print_r($employees->toArray());
    }
}
