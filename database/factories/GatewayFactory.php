<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class GatewayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->md5(),
            'sys_uptime' => fake()->randomNumber(),
            'sys_memfree' => fake()->randomNumber(),
            'sys_load' => fake()->randomFloat(2, 0, 100),
            'wifidog_uptime' => fake()->randomNumber(),
        ];
    }
}
