<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StpUser extends Model
{

    protected $attributes = [
        'name' => '',
        'gender' => '1',
        'birthday' => '',
        'ethnic_bg' => '',
        'height' => 0,
        'weight' => 0
    ];

    public static $genders = [
        '1' => 'male',
        '2' => 'female',
        '3' => 'other'
    ];

    public static function createToken($id)
    {
        if (empty($id)) {
            return '';
        }
        $data = [
            'uid' => $id,
            'v' => '1.0'
        ];
        $str = json_encode($data) . config('jwt.secret');
        $token = base64_encode($str);
        return $token;
    }

    public static function getIdFromToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $token = substr($token, 7);// 去掉Bearer
        $str = base64_decode($token);
        $data = str_replace(config('jwt.secret'), '', $str);
        if (empty($data)) {
            return false;
        }
        $data = json_decode($data, true);
        if (!isset($data['uid'])) {
            return false;
        }
        return $data['uid'];
    }

    public static function getAvatar($avatar)
    {
        if (empty($avatar)) {
            return config('app.url') . '/public/imgs/avatar.png';
        }
        $url = fullFileUrl($avatar);
        return $url;
    }

    /**
     * 格式化用户信息.
     * 用于显示时展示的gender、age、name等信息
    */
    public static function formatUserInfo($stp_user)
    {
        if (empty($stp_user)) {
            return [];
        }
        if (!is_array($stp_user)) {
            $stp_user = $stp_user->toArray();
        }
        if (empty($stp_user['name'])) {
            $stp_user['name'] = explode('@', $stp_user['email'])[0];
        }
        $gender = self::$genders[$stp_user['gender']] ?? '';
        $stp_user['gender'] = strtoupper(substr($gender, 0, 1)) . substr($gender, 1);
        $stp_user['age'] = calcAge($stp_user['birthday']);
        $stp_user['avatar'] = self::getAvatar('');
        return $stp_user;
    }
}
