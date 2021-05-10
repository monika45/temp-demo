<?php

namespace App\Http\Controllers;

use App\Model\StpUser;
use Illuminate\Http\Request;

class StpController extends Controller
{

    public function responseJson($data, $status = 200)
    {
        $result = [
            'err' => 0,
            'msg' => 'success'
        ];
        if (isset($data['err'])) {
            $result['err'] = $data['err'];
            unset($data['err']);
        }
        if (isset($data['msg'])) {
            $result['msg'] = $data['msg'];
            unset($data['msg']);
        }
        if (array_key_exists('data', $data)) {
            $result['data'] = $data['data'];
        } else {
            $result['data'] = $data;
        }
        if (empty($result['data'])) {
            $result['data'] = null;
        }
        return response()->json($result, $status);
    }


    /**
     * 从当前请求头中获取用户信息
     * @param bool $strict 没有用户ID时，是否报错
     * @return false|uid
     */
    public function getUid($strict = true)
    {
        $request = request();
        $token = $request->header('authorization');
        $uid = StpUser::getIdFromToken($token);
        if (empty($uid)) {
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode([
                'err' => STP_ERR_UNAUTHORIZED,
                'msg' => 'unauthorized',
                'data' => null
            ]);die;
        }
        return $uid;
    }
}
