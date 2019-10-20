<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use sinkcup\LaravelUiSocialite\SocialAccount;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testEdit()
    {
        $user = factory(User::class)->create();
        $social_account = factory(SocialAccount::class)->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->get('/settings/profile');

        $response->assertViewIs('settings.profile');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('social_login_providers', config('auth.social_login.providers'));
        $response->assertViewHas('linked_providers', [$social_account->provider]);
    }

    public function testUpdate()
    {
        $user = factory(User::class)->create();
        $data = [
            'email' => $this->faker->safeEmail,
            'name' => $this->faker->name,
        ];
        $response = $this->actingAs($user)->put('/settings/profile', $data);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertEquals($data['email'], $user->email);
        $this->assertEquals($data['name'], $user->name);
    }

    public function testRedirectToLogin()
    {
        $response = $this->get('/home');

        $response->assertStatus(302)->assertRedirect(route('login'));
    }

    public function testJsonNotRedirectToLogin()
    {
        $response = $this->getJson('/home');

        $response->assertStatus(401);
    }
}
