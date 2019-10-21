<?php

namespace Tests\Unit;

use App\User;
use Redis;
use RedisManager;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }

    public function testDatabase()
    {
        $user1 = factory(User::class)->create();
        $name = 'user2';
        $email = 'user2@example.com';
        factory(User::class)->create(compact('name', 'email'));
        $this->assertEquals(2, User::count());
        $this->assertEquals($user1->toArray(), User::first()->toArray());
        $this->assertEquals($name, User::where('email', $email)->first()->name);
    }
}
