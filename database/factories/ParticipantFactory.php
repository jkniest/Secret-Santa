<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Models\Participant::class, function (Faker $faker) {
    return [
        'discord_user_id' => $faker->unique()->uuid
    ];
});
