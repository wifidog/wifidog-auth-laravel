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

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleRegisterSuccess()
    {
        $data = [
            'name' => 'Joe',
            'email' => 'joe@example.com',
            'password' => 'passwordtest',
            'password_confirmation' => 'passwordtest'
        ];

        $response = $this->post('/register', $data);
        $response->assertRedirect('/home');

        //Remove password and password_confirmation from array
        array_splice($data, 2, 2);
        $this->assertDatabaseHas('users', $data);
        $result = User::where('email', $data['email'])->get();
        $this->assertEquals(1, count($result));
        $user_in_db = $result[0];
        $this->assertEquals($data['name'], $user_in_db->name);
        $this->assertNotEmpty($user_in_db->api_token);
    }
}
