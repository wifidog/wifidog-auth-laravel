<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;

class WelcomeControllerTest extends TestCase
{
    public function testIndex()
    {
        $params = [
            'gw_address' => $this->faker->ipv4,
            'gw_port' => $this->faker->numberBetween(1025, 9999),
            'url' => $this->faker->url,
        ];
        $response = $this->call('GET', "/", $params);
        $response->assertStatus(200);
        $response->assertSessionHasAll($params);
    }
}
