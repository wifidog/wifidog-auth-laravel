<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->withSession([
                'gw_address' => '192.168.199.1',
                'gw_port' => '2060',
            ])->get('/home');
        $response->assertStatus(200)->assertViewHas('wifidog_uri');
    }

    public function testIndexWithoutWifidog()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/home');
        $response->assertStatus(200)->assertViewMissing('wifidog_uri');
    }
}
