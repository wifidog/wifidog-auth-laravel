<?php

namespace Tests\Feature;

use App;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Str;
use Mockery;
use sinkcup\LaravelMakeAuthSocialite\SocialAccount;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Socialite;

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

        $token = Str::random(60);
        $mock= $this->createPartialMock(LoginController::class, ['generateToken']);
        $mock->expects($this->once())
            ->method('generateToken')
            ->willReturn($token);
        App::instance(LoginController::class, $mock);

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
        //$response = $this->call('GET', "/login/$driver/callback", $params);
        //$response->assertRedirect('/home');

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

    public function testGenerateToken()
    {
        $controller = new LoginController();
        $this->assertEquals(60, strlen($controller->generateToken()));
    }
}
