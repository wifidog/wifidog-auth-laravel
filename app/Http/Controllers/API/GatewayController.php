<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Gateway;
use Validator;

class GatewayController extends Controller
{
    /**
     * Gateway heartbeating (Ping Protocol)
     *
     * @example curl 'http://wifidog-auth.lan/ping?gw_id=001217DA42D2&sys_uptime=742725&sys_memfree=2604&sys_load=0.03&wifidog_uptime=3861'
     * @link http://dev.wifidog.org/wiki/doc/developer/WiFiDogProtocol_V1#GatewayheartbeatingPingProtocol
     *
     * @return \Illuminate\Http\Response
     */
    public function ping(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gw_id' => 'required|string',
            'sys_uptime' => 'required|integer',
            'sys_memfree' => 'required|integer',
            'sys_load' => 'required|numeric',
            'wifidog_uptime' => 'required|integer',
        ]);
        $responseTxt = 'Error: params wrong';
        if ($validator->fails()) {
            return response()->txt($responseTxt, 400);
        }
        $input = $validator->getData();
        $input['id'] = $input['gw_id'];
        unset($input['gw_id']);
        $statusCode = 200;
        if (config('wifidog.allow_unknown_gateway')) {
            Gateway::updateOrCreate(['id' => $input['id']], $input);
            $responseTxt = 'Pong';
            return response()->txt($responseTxt, $statusCode);
        }
        $gateway = Gateway::find($input['id']);
        if (!empty($gateway)) {
            $gateway->update($input);
            $responseTxt = 'Pong';
            return response()->txt($responseTxt, $statusCode);
        }
        $responseTxt = 'Error: not allow unknown gateway';
        $statusCode = 400;
        return response()->txt($responseTxt, $statusCode);
    }
}
