<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Support\Str;

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
        $user = factory(User::class)->create();
        return $user->api_token;
    }

    public function testLoginSuccess()
    {
        $params = [
            'stage' => 'login',
            'token' => $this->grantToken(),
            'gw_id' => 'D4EE073700C2',
        ];
        $response = $this->call('GET', '/auth/', $params);

        $response->assertStatus(200);
        $this->assertEquals('Auth: 1', $response->getContent());
    }

    public function testLoginWithBadToken()
    {
        $params = [
            'stage' => 'login',
            'token' => 'thisIsABadToken',
            'gw_id' => 'D4EE073700C2',
        ];
        $response = $this->call('GET', '/auth/', $params);

        $response->assertStatus(401);
        $this->assertEquals('Auth: 0', $response->getContent());
    }

    public function testCount()
    {
        $params = [
            'stage' => 'counters',
            'token' => $this->grantToken(),
            'gw_id' => 'D4EE073700C2',
            'incoming' => rand(1, 999),
            'outgoing' => rand(1, 999),
            'ip' => $this->faker->ipv4(),
            'mac' => $this->faker->macAddress(),
        ];
        $response = $this->call('GET', '/auth/', $params);

        $response->assertStatus(200);
        $this->assertEquals('Auth: 1', $response->getContent());
    }
}
