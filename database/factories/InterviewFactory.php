<?php

use Faker\Generator as Faker;

$factory->define(App\Interview::class, function (Faker $faker) {
    return [
        'subject' => $faker->slug,
        'place' => $faker->address,
        'synthesis' => $faker->realText()
    ];
});
