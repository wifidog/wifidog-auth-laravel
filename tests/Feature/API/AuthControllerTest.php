<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthLostParams()
    {
        $params = [
            'stage' => 'login',
            'gw_id' => 'D4EE073700C2',
        ];
        $response = $this->call('GET', '/auth/', $params);

        $response->assertStatus(401);
        $this->assertEquals('Auth: 0', $response->getContent());
    }

    public function grantToken()
    {
        $user = User::factory()->create();
        return $user->createToken('wifidog', ['internet:access'])->plainTextToken;
    }

    public function testLoginSuccess()
    {
        $params = [
            'stage' => 'login',
            'token' => $this->grantToken(),
        ];
        $response = $this->call('GET', '/auth', $params);

        $response->assertStatus(200);
        $this->assertEquals('Auth: 1', $response->getContent());
    }

    public function testLoginWithBadToken()
    {
        $params = [
            'stage' => 'login',
            'token' => 'thisIsABadToken',
        ];
        $response = $this->call('GET', '/auth', $params);

        $response->assertStatus(401);
        $this->assertEquals('Auth: 0', $response->getContent());
    }

    public function testCount()
    {
        $params = [
            'stage' => 'counters',
            'token' => $this->grantToken(),
            'incoming' => rand(1, 999),
            'outgoing' => rand(1, 999),
            'ip' => fake()->ipv4(),
            'mac' => fake()->macAddress(),
        ];
        $response = $this->call('GET', '/auth', $params);

        $response->assertStatus(200);
        $this->assertEquals('Auth: 1', $response->getContent());
    }
}
