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
        self::user();
        $user = [
            'name' => 'testInit',
            'email' => 'testInit@test.com',
            'password' => bcrypt('test'),
            'remember_token' => str_random(10),
        ];
        plunner\User::create($user);

    }

    static private function user()
    {
        factory(plunner\User::class, 50)->create()->each(function ($u) {
            return ;
        });
    }
}
