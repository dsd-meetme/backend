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
        $company = [
            'name' => 'testInit',
            'email' => 'testInit@test.com',
            'password' => bcrypt('test'),
            'remember_token' => str_random(10),
        ];
        $company = plunner\Company::create($company);
        self::employees($company);
        $employee = new \plunner\Employee([
            'name' => 'testEmp',
            'email' => 'testEmp@test.com',
            'password' => bcrypt('test'),
            'remember_token' => str_random(10),
        ]);
        $company->employees()->save($employee);
        self::calendars($employee);
    }

    static private function company()
    {
        factory(plunner\Company::class, 10)->create()->each(function ($company) {
            self::employees($company);
        });
    }

    static private function employees($company)
    {
        factory(plunner\Employee::class, 3)->make()->each(function ($employee) use($company){
            $company->employees()->save($employee);
            self::calendars($employee);
        });
    }

    static private function calendars($employee)
    {
        factory(plunner\Calendar::class, 3)->make()->each(function ($calendar) use($employee){
            $employee->calendars()->save($calendar);
        });
    }
}
