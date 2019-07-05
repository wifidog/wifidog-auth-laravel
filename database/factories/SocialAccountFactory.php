<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;
use sinkcup\LaravelMakeAuthSocialite\SocialAccount;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(SocialAccount::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'provider' => $faker->word,
        'provider_user_id' => $faker->word . $faker->randomNumber(),
        'access_token' => $faker->md5,
        'refresh_token' => null,
        'expires_in' => null,
        'nickname' => $faker->lastName,
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'avatar' => $faker->imageUrl(),
        'raw' => null,
    ];
});
