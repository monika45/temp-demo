<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
        return response()->json($result, $status);
    }


    public function responseSuccess($data, $status = 200)
    {
        return $this->responseJson([
            'err' => 0,
            'msg' => 'success',
            'data' => $data
        ], $status);
    }

    public function responseError($msg, $data = [], $status = 200)
    {
        return $this->responseJson([
            'err' => 1,
            'msg' => $msg,
            'data' => $data
        ], $status);
    }

    public function responseErrorWithCode($errcode, $msg)
    {
        return $this->responseJson([
            'err' => $errcode,
            'msg' => $msg,
            'data' => []
        ], 200);
    }

    public function responseSuccessMsg($msg)
    {
        return $this->responseJson([
            'err' => 0,
            'msg' => $msg,
            'data' => []
        ], 200);
    }

    /**
     * 当前授权的用户
    */
    public function authUser()
    {
        $user = auth('api')->user();
        return $user;
    }

    /**
     * 当前授权用户的所属企业
    */
    public function authUserCompanyId()
    {
        $user = $this->authUser();
        if (empty($user)) {
            return 0;
        }
        return $user->company_id;
    }
}
