<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Gateway;

class GatewayControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testPing()
    {
        $params = [
            'gw_id' => fake()->word(),
            'sys_uptime' => rand(1, 999),
            'sys_memfree' => rand(1, 999),
            'sys_load' => fake()->randomFloat(2, 0, 100),
            'wifidog_uptime' => rand(1, 999),
        ];
        $response = $this->call('GET', '/ping/', $params);

        $response->assertStatus(200);
        $this->assertEquals('Pong', $response->getContent());

        $gateways = Gateway::get();
        $this->assertEquals(1, count($gateways));
        $gateway = $gateways->first()->toArray();
        $tmp = $params;
        $tmp['id'] = $params['gw_id'];
        unset($tmp['gw_id']);
        foreach ($tmp as $k => $v) {
            $this->assertEquals($v, $gateway[$k]);
        }
    }

    public function testPingWithBadParams()
    {
        $params = [
            'gw_id' => fake()->word(),
            'sys_uptime' => rand(1, 999),
            'sys_memfree' => rand(1, 999),
            'sys_load' => fake()->randomFloat(2, 0, 100),
            'wifidog_uptime' => rand(1, 999),
        ];
        $key = array_rand($params);
        $response = $this->call('GET', '/ping/', [$key => $params[$key]]);

        $response->assertStatus(400);
        $this->assertEquals('Error: params wrong', $response->getContent());
    }

    public function testPingFailedWhenNotAllowUnknownGateway()
    {
        config(['wifidog.allow_unknown_gateway' => false]);
        $params = [
            'gw_id' => fake()->word(),
            'sys_uptime' => rand(1, 999),
            'sys_memfree' => rand(1, 999),
            'sys_load' => fake()->randomFloat(2, 0, 100),
            'wifidog_uptime' => rand(1, 999),
        ];
        $response = $this->call('GET', '/ping/', $params);

        $response->assertStatus(400);
        $this->assertEquals('Error: not allow unknown gateway', $response->getContent());
    }

    public function testPingExistsGatewayWhenNotAllowUnknownGateway()
    {
        config(['wifidog.allow_unknown_gateway' => false]);
        $gateway = Gateway::factory()->create();
        $params = [
            'gw_id' => $gateway->id,
            'sys_uptime' => rand(1, 999),
            'sys_memfree' => rand(1, 999),
            'sys_load' => fake()->randomFloat(2, 0, 100),
            'wifidog_uptime' => rand(1, 999),
        ];
        $response = $this->call('GET', '/ping/', $params);

        $response->assertStatus(200);
        $this->assertEquals('Pong', $response->getContent());

        $gateways = Gateway::get();
        $this->assertEquals(1, count($gateways));
        $gateway = $gateways->first()->toArray();
        $tmp = $params;
        $tmp['id'] = $params['gw_id'];
        unset($tmp['gw_id']);
        foreach ($tmp as $k => $v) {
            $this->assertEquals($v, $gateway[$k]);
        }
    }
}
