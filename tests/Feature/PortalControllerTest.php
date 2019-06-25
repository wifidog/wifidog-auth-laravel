<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class PortalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $user = factory(User::class)->create();
        $uri = $this->faker->url();
        $response = $this->actingAs($user)
            ->withSession([
                'url' => $uri,
            ])->get('/portal/');

        $response->assertStatus(302)->assertRedirect($uri);
    }

    public function testRedirectToConfigUri()
    {
        $user = factory(User::class)->create();
        $uri = $this->faker->url();
        config(['wifidog.portal_redirect_uri' => $uri]);
        $response = $this->actingAs($user)->get('/portal/');

        $response->assertStatus(302)->assertRedirect($uri);
    }
}
