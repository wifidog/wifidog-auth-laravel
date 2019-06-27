<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Gateway;
use Faker\Generator as Faker;

$factory->define(Gateway::class, function (Faker $faker) {
    return [
        'id' => $faker->md5,
        'sys_uptime' => $faker->randomNumber(),
        'sys_memfree' => $faker->randomNumber(),
        'sys_load' => $faker->randomFloat(2, 0, 100),
        'wifidog_uptime' => $faker->randomNumber(),
    ];
});
