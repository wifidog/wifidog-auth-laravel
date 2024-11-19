<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $user = User::factory()->create();
        $token = $user->createToken('wifidog', ['internet:access'])->plainTextToken;

        $response = $this->actingAs($user)
            ->withSession([
                'gw_address' => '192.168.199.1',
                'gw_port' => '2060',
                'token' => $token,
            ])->get('/dashboard');
        $wifidogUri = "http://192.168.199.1:2060/wifidog/auth?token=" . $token;
        $response->assertStatus(200)->assertViewHas('wifidog_uri', $wifidogUri);
        $response->assertSeeText('start internet');
    }

    public function testIndexWithoutWifidog()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200)->assertViewMissing('wifidog_uri');
    }
}
