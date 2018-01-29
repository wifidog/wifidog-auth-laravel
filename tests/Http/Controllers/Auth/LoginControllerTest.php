<?php

namespace Tests\Http\Controllers\Auth;

use App\SocialUser;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use Meta;
use Socialite;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;
    
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
            ->shouldReceive('getName')
            ->andReturn($user_name);

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstract_user);

        $driver = 'facebook';
        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);
        $params = [
            'state' => '{st=' . $this->faker->word . ',ds=' . $this->faker->randomNumber() . '}',
        ];
        $response = $this->call('GET', "/login/$driver/callback", $params);
        $response->assertRedirect('/home');

        $result = SocialUser::where('provider', $driver)->where('provider_user_id', $provider_user_id)->get();
        $this->assertEquals(1, count($result));
        $social_user_in_db = $result[0];

        $result = User::where('email', $driver . '.' . $provider_user_id . '@example.com')->get();
        $this->assertEquals(1, count($result));
        $user_in_db = $result[0];
        $this->assertEquals($user_name, $user_in_db->name);
        $this->assertEquals($social_user_in_db->user_id, $user_in_db->id);
    }
}
