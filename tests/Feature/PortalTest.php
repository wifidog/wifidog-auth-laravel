<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $user = User::factory()->create();
        $uri = fake()->url();
        $response = $this->actingAs($user)
            ->withSession([
                'url' => $uri,
            ])->get('/portal/');

        $response->assertStatus(302)->assertRedirect($uri);
    }

    public function testRedirectToConfigUri()
    {
        $user = User::factory()->create();
        $uri = fake()->url();
        config(['wifidog.portal_redirect_uri' => $uri]);
        $response = $this->actingAs($user)->get('/portal/');

        $response->assertStatus(302)->assertRedirect($uri);
    }
}
