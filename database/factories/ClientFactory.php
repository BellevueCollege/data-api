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

/*$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});*/
use Illuminate\Support\Facades\Hash;

$factory->define(App\Models\Client::class, function (Faker\Generator $faker) {
    return [
        'clientname' => $faker->name,
        'clientid' => $faker->uuid(),
        'clienturl' => $faker->url(),
        'password' => Hash::make('9b1d89ea-02bf-11e7-93ae-92361f002671'),
    ];
});
