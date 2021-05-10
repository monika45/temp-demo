<?php

namespace App\Http\Controllers;

use App\Model\StpMonitorData;
use App\Model\StpUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StpUserController extends StpController
{
    /**
     * 登录注册
     * type: 1-登录 2-注册
    */
    public function login(Request $request)
    {
        $type = $request->input('type', 2);
        $email = $request->input('email');
        $password = $request->input('password');
        if (!in_array($type, [1, 2])) {
            return $this->responseError('Wrong type.');
        }
        if (empty($email)) {
            return $this->responseError('Email is required.');
        }
        if (empty($password) || strlen($password) < 8) {
            return $this->responseError('Password should be at least 8 characters.');
        }
        $user = StpUser::where('email', $email)->first();
        if ($type == 2) {
            // 注册
            if ($user) {
                return $this->responseErrorWithCode(STP_ERR_EMAIL_EXIST, 'This email has been used.');
            }
            $user = new StpUser();
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();
        }
        if ($type == 1) {
            // 登录
            if (empty($user)) {
                return $this->responseErrorWithCode(STP_ERR_EMAIL_NOTEXIST, 'This email has not been registered yet.');
            }
            if (!Hash::check($password, $user->password)) {
                return $this->responseError('Email or password error.');
            }
        }

        return $this->responseSuccess(['token' => StpUser::createToken($user->id)]);


    }

    /**
     * 获取用户数据
    */
    public function getUserData(Request $request)
    {
        $uid = $this->getUid(true);
        $user = StpUser::find($uid);
        if (!$user) {
            return $this->responseError('User does not exist.');
        }
        $res = [
            'email' => $user->email,
            'avatar' => StpUser::getAvatar(''),
            'name' => $user->name,
            'gender' => $user->gender,
            'birthday' => $user->birthday,
            'ethnic_bg' => $user->ethnic_bg,
            'height' => $user->height,
            'weight' => $user->weight,
            'blood_type' => $user->blood_type
        ];
        return $this->responseSuccess($res);
    }

    /**
     * 保存用户数据
    */
    public function modifyUserData(Request $request)
    {
        $uid = $this->getUid(true);
        $user = StpUser::find($uid);
        if (!$user) {
            return $this->responseError('User does not exist.');
        }
        try {
            $user->name = $request->input('name') ?? '';
            $user->gender = $request->input('gender') ?? '1';
            $user->birthday = $request->input('birthday') ?? '';
            $user->ethnic_bg = $request->input('ethnic_bg') ?? '';
            $user->height = $request->input('height') ?? 0;
            $user->weight = $request->input('weight') ?? 0;
            $user->blood_type = $request->input('blood_type') ?? '';
            $user->save();
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
        return $this->responseSuccessMsg('My data saved.');
    }


    /**
     * 提交温度数据
    */
    public function submitTemperature(Request $request)
    {
        $uid = $this->getUid(true);
        $temperature = $request->input('temperature');
        $day = $request->input('day');
        $time = $request->input('time');
        $location = $request->input('location');//完整地址
        $city = $request->input('city');//城市
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        if (empty($temperature)) {
            return $this->responseError('Temperature cannot be empty.');
        }
        if (empty($day)) {
            return $this->responseError('Date cannot be empty.');
        }
        if (!checkDateFormat($day)) {
            return $this->responseError('Wrong date format.');
        }
        if (!checkTimeFormat($time)) {
            return $this->responseError('Wrong time format.');
        }
        if (empty($time)) {
            return $this->responseError('Time cannot be empty.');
        }
        if (empty($location) || empty($lat) || empty($lng)) {
            return $this->responseError('Location info cannot be empty.');
        }
        $location_data = empty($city) ? $location : $city;
        try {
            $where = [
                'user_id' => $uid,
                'day' => $day,
                'time' => $time,
                'type' => 'temperature'
            ];
            $data = array_merge($where, [
                'data' => $temperature,
                'variation' => StpMonitorData::getVariation($uid, $where['type'], $temperature, $day, $time),
                'created_at' => date('Y-m-d H:i:s'),
                'location' => $location_data,
                'lat' => $lat,
                'lng' => $lng
            ]);
            StpMonitorData::updateOrCreate($where, $data);
            //如果当前添加的时刻后面已经有提交了的，要更新后一个的变化值
            $laterData = StpMonitorData::where('user_id', $uid)
                ->where('type', 'temperature')
                ->whereRaw('concat(`day`, `time`) > ?', [$day . $time])
                ->orderBy('day', 'asc')
                ->orderBy('time', 'asc')
                ->first();
            if ($laterData) {
                $laterData->variation = StpMonitorData::calVariation($laterData->data, $temperature);
                $laterData->save();
            }
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
        return $this->responseSuccessMsg('Temperature data submitted successfully.');
    }

    /**
     * 删除监控数据
    */
    public function delMonitorData(Request $request)
    {
        $uid = $this->getUid(true);
        $id = $request->input('id');
        $day = $request->input('day');
        $time = $request->input('time');
        $type = $request->input('type', 'temperature');
        //有ID直接删
        if ($id) {
            StpMonitorData::where('id', $id)->where('user_id', $uid)->delete();
            return $this->responseSuccessMsg('Done.');
        }
        //否则按条件删除
        if (empty($day) || empty($time) || empty($type)) {
            return $this->responseError('Wrong param.');
        }
        if (!in_array($type, ['temperature', 'pulse'])) {
            return $this->responseError('Wrong type.');
        }
        if (!checkDateFormat($day)) {
            return $this->responseError('Wrong date format.');
        }
        if (!checkTimeFormat($time)) {
            return $this->responseError('Wrong time format.');
        }
        StpMonitorData::where('user_id', $uid)
            ->where('day', $day)
            ->where('time', $time)
            ->where('type', $type)
            ->delete();
        return $this->responseSuccessMsg('Done.');

    }


    /**
     * 监控数据列表
     * 按日期分页，每次查询10天的数据，最多能查前一年的数据
     * 注意：不会出现中间空页的情况，如：数据库中仅存在2020-01-01和2020-05-01这两天的数据，将会在第一页查询出来。
    */
    public function monitorDataList(Request $request)
    {
        $uid = $this->getUid(true);
        $page = $request->input('page', 1);
        $pageSize = 10;//每次查10天的数据

        $days = StpMonitorData::where('user_id', $uid)
            ->where('day', '>=', date('Y-m-d', strtotime("-1 year")))
            ->groupBy('day')->orderBy('day', 'desc')->pluck('day')->toArray();
        $days_chunk = array_chunk($days, $pageSize);
        $totalPage = count($days_chunk);//总页数
        if ($page > $totalPage) {
            return $this->responseSuccess([]);
        }

        $start = $days_chunk[$page - 1][count($days_chunk[$page - 1]) - 1];
        $end = $days_chunk[$page - 1][0];
        $datas = StpMonitorData::where('user_id', $uid)
            ->whereBetween('day', [$start,  $end])
            ->orderBy('day', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        // 列表数据过滤
        $result = [];
        foreach ($datas as $d) {
            $day_str = 'Today';
            if ($d->day != date('Y-m-d')) {
                $day_str = date('d M, Y', strtotime($d->day));
            }
            $item = [
                'id' => $d->id,
                'time' => $d->time,
                'type' => $d->type,
                'data' => $d->data . StpMonitorData::$dataUnits[$d->type] ?? '',
                'variation' => $d->variation
            ];
            if (array_key_exists($d->day, $result)) {
                $result[$d->day]['list'][] = $item;
            } else {
                $result[$d->day] = [
                    'day' => $day_str,
                    'list' => [$item]
                ];
            }
        }
        if ($result) {
            $result = array_values($result);
        }
        return $this->responseSuccess($result);
    }


    /**
     * 获取一天的温度数据
    */
    public function dailyTemperatures(Request $request)
    {
        $uid = $this->getUid(true);
        $day = $request->input('day');
        if (empty($day)) {
            $day = date('Y-m-d');
        }
        $data = StpMonitorData::where('user_id', $uid)
            ->where('type', 'temperature')
            ->where('day', $day)
            ->orderBy('time', 'desc')
            ->select('id', 'time', 'data')
            ->get()->toArray();
        $unit = StpMonitorData::$dataUnits['temperature'];
        $result = [
            'day' => $day,
            'avg' => '0' . $unit,
            'min' => '0' . $unit,
            'max' => '0' . $unit,
            'list' => []
        ];
        if (empty($data)) {
            return $this->responseSuccess($result);
        }
        $temperatures = array_column($data, 'data');
        $result['avg'] = round(array_sum($temperatures) / count($temperatures), 1) . $unit;
        $result['min'] = min($temperatures) . $unit;
        $result['max'] = max($temperatures) . $unit;
        foreach ($data as $d) {
            $d['data'] = $d['data'] . $unit;
            $result['list'][] = $d;
        }
        return $this->responseSuccess($result);

    }








}
