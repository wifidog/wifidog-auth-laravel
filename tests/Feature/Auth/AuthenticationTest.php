<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginScreenCanBeRendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSeeText('Log in');
        $response->assertSeeText('Register?');
    }

    public function testUsersCanAuthenticateUsingTheLoginScreen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function testUsersCanNotAuthenticateWithInvalidPassword(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function testUsersCanLogout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function testLogoutWithWifidog()
    {
        $user = User::factory()->create();
        $token = $user->createToken('wifidog', ['internet:access'])->plainTextToken;
        $gwAddress = fake()->ipv4;
        $gwPort = fake()->numberBetween(1025, 9999);
        $response = $this->actingAs($user)
            ->withSession([
                'gw_address' => $gwAddress,
                'gw_port' => $gwPort,
                'token' => $token,
            ])->post('/logout');
        $response->assertRedirect('http://' . $gwAddress . ':' . $gwPort . '/wifidog/auth?logout=1&token='
            . $token);
    }

    public function testUsersCanBeRedirectToWifidogGatewayAfterLogin(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession([
            'gw_address' => '192.168.199.1',
            'gw_port' => '2060',
        ])->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('http://192.168.199.1:2060/wifidog/auth?token=' . session('token'));
    }
}
