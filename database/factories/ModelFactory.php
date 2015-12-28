<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(plunner\Company::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});


$factory->define(plunner\Employee::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(plunner\Calendar::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'enabled' => $faker->boolean(),
    ];
});


$factory->define(plunner\Timeslot::class, function (Faker\Generator $faker) {
    return [
        'time_start' => $faker->dateTime,
        'time_end' => $faker->dateTime,
    ];
});

$factory->define(plunner\Group::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->sentence . $faker->sentence,
    ];
});

$factory->define(plunner\Meeting::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->name,
        'description' => $faker->sentence . $faker->sentence,
        'duration' => $faker->numberBetween(0,100),
    ];
});

$factory->define(plunner\MeetingTimeslot::class, function (Faker\Generator $faker) {
    return [
        'time_start' => $faker->dateTime,
        'time_end' => $faker->dateTime,
    ];
});

$factory->define(plunner\Caldav::class, function (Faker\Generator $faker) {
    return [
        'url' => 'http://test.com',
        'username' => 'test',
        'password' => 'test',
        'calendar_name' => 'test',
    ];
});