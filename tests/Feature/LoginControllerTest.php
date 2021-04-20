<?php

namespace Tests\Feature;

use Mockery;
use LaravelFans\UiSocialite\SocialAccount;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Socialite;
use phpmock\MockBuilder;
use phpmock\functions\FixedValueFunction;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleProviderCallbackFailedWithInvalidState()
    {
        $params = [
            'state' => '{st=state123abc,ds=123456789}',
        ];
        $response = $this->call('GET', '/login/facebook/callback', $params);

        $response->assertRedirect('/login');
    }

    public function testHandleProviderCallbackSuccess()
    {
        $abstract_user = Mockery::mock('Laravel\Socialite\Two\User');
        $abstract_user->token = $this->faker->word;
        $provider_user_id = $this->faker->randomNumber();
        $user_name = $this->faker->name;
        $abstract_user->shouldReceive('getId')
            ->andReturn($provider_user_id)
            ->shouldReceive('getNickname')
            ->andReturn($user_name)
            ->shouldReceive('getEmail')
            ->andReturn(null)
            ->shouldReceive('getRaw')
            ->andReturn(null)
            ->shouldReceive('getAvatar')
            ->andReturn($user_name)
            ->shouldReceive('getName')
            ->andReturn($user_name);

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstract_user);
        $provider->shouldReceive('scopes')->andReturn($provider);

        $builder = new MockBuilder();
        $token = $this->faker->regexify('[A-Za-z0-9]{80}');
        $builder->setNamespace('App\Http\Controllers\Auth')
        ->setName("bin2hex")
        ->setFunctionProvider(new FixedValueFunction($token));

        $mock = $builder->build();
        $mock->enable();

        $driver = 'facebook';
        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);
        $params = [
            'state' => '{st=' . $this->faker->word . ',ds=' . $this->faker->randomNumber() . '}',
        ];
        $gw_address = $this->faker->ipv4;
        $gw_port = $this->faker->numberBetween(1025, 9999);
        $response = $this->withSession([
                'gw_address' => $gw_address,
                'gw_port' => $gw_port,
            ])->get("/login/$driver/callback?" . http_build_query($params));
        $response->assertRedirect('http://' . $gw_address . ':' . $gw_port . '/wifidog/auth?token=' . $token);

        $result = SocialAccount::where('provider', $driver)->where('provider_user_id', $provider_user_id)->get();
        $this->assertEquals(1, count($result));
        $social_user_in_db = $result[0];

        $result = User::where('email', $driver . '.' . $provider_user_id . '@example.com')->get();
        $this->assertEquals(1, count($result));
        $user_in_db = $result[0];
        $this->assertEquals($user_name, $user_in_db->name);
        $this->assertEquals($social_user_in_db->user_id, $user_in_db->id);
    }

    public function testParamsToSession()
    {
        $params = [
            'gw_address' => $this->faker->ipv4,
            'gw_port' => $this->faker->numberBetween(1025, 9999),
            'url' => $this->faker->url,
        ];
        $response = $this->call('GET', "/login", $params);
        $response->assertStatus(200);
        $response->assertSessionHasAll($params);
    }

    public function testRedirectIfAuthenticated()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
            ->get('/login');
        $response->assertRedirect('/home');
    }

    public function testLogout()
    {
        $user = factory(User::class)->create();

        $gw_address = $this->faker->ipv4;
        $gw_port = $this->faker->numberBetween(1025, 9999);
        $response = $this->actingAs($user)
            ->withSession([
                'gw_address' => $gw_address,
                'gw_port' => $gw_port,
            ])->post('/logout');
        $response->assertRedirect('http://' . $gw_address . ':' . $gw_port . '/wifidog/auth?logout=1&token='
            . $user->api_token);
    }

    public function testLogoutWithoutWifidog()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->post('/logout');
        $response->assertRedirect('/');
    }
}
