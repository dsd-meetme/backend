<?php

use Illuminate\Database\Seeder;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        self::company();
        self::makeDataKnown();

    }

    static private function makeDataKnown()
    {
        //create company
        $company = [
            'name' => 'testInit',
            'email' => 'testInit@test.com',
            'password' => bcrypt('test'),
            'remember_token' => str_random(10),
        ];
        $company = plunner\Company::create($company);

        //create employees
        self::employees($company);
        $employee = new \plunner\Employee([
            'name' => 'testEmp',
            'email' => 'testEmp@test.com',
            'password' => bcrypt('test'),
            'remember_token' => str_random(10),
        ]);
        $company->employees()->save($employee);

        //create groups
        self::groups($company, $company->employees->toArray());
    }

    static private function company()
    {
        factory(plunner\Company::class, 10)->create()->each(function ($company) {
            self::employees($company);
            self::groups($company, $company->employees->toArray());
        });
    }

    static private function employees($company)
    {
        factory(plunner\Employee::class, 3)->make()->each(function ($employee) use ($company) {
            $company->employees()->save($employee);
        });
    }

    static private function groups($company, $employees)
    {
        factory(plunner\Group::class, 2)->make()->each(function ($group) use ($company, $employees) {
            $employeeSubsetIndices = array_rand($employees, rand(1, 3)); // 1 to 3 random members in each team
            $employeeSubsetIndices = is_array($employeeSubsetIndices) ? $employeeSubsetIndices : [$employeeSubsetIndices];
            $employeeSubset = array_map(function ($index) use ($company) {
                return $company->employees[$index];
            }, $employeeSubsetIndices);

            $plannerIndex = array_rand($employeeSubset);
            $employeePlanner = $company->employees[$plannerIndex];

            /**
             * @var $group \plunner\Group
             */
            $group->planner_id = $company->employees[$plannerIndex]->id;
            $company->groups()->save($group);

            array_map(function ($employee) use ($group) {
                $group->employees()->save($employee);
            }, $employeeSubset);
        });
    }
}
