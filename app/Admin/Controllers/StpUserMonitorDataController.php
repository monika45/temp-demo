<?php


namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Model\StpMonitorData;
use App\Model\StpUser;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StpUserMonitorDataController extends Controller
{
    public function index(Content $content, $id)
    {
        $token = StpUser::createToken($id);
        $content->title('Monitor Data');
        $content->description('Body temperature, Pulse');
        $content->view('admin.stp-user-monitor-data', [
            'token' => $token,
            'today' => date('Y-m-d')
        ]);
        return $content;

    }

    /**
     * 非正常体温的前10个城市
    */
    public function abnormalTopCities()
    {
        $data = StpMonitorData::whereNotBetween('data', [35, 37])
            ->select(DB::raw('location'), DB::raw('count(*) num'))
            ->groupBy('location')
            ->orderBy('num', 'desc')
            ->get()->toArray();
        foreach ($data as $k => $v) {
            if (empty($v['location'])) {
                $v['location'] = 'Unknown';
            }
            $data[$k] = $v;
        }
        return $this->responseSuccess($data);
    }


    /**
     * 分页获取体温数据列表
     * 页码：page
    */
    public function dataRecords(Request $request)
    {
        $data = StpMonitorData::with('user:id,name,email,gender,birthday')
            ->where('type', 'temperature')
            ->orderBy('day', 'desc')
            ->orderBy('time', 'desc')
            ->select('id', 'user_id', 'day', 'time', 'location', 'data')
            ->paginate(10)->toArray();
        foreach ($data['data'] as $k => $v) {
            if (!empty($v['user'])) {
                $gender = StpUser::$genders[$v['user']['gender']];
                $gender = strtoupper(substr($gender, 0, 1)) . substr($gender, 1);
                $name = $v['user']['name'];
                if (empty($name)) {
                    $name = explode('@', $v['user']['email'])[0];
                }
                $data['data'][$k]['name'] = $name;
                $data['data'][$k]['gender'] = $gender;
                $data['data'][$k]['age'] = calcAge($v['user']['birthday']);
                unset($data['data'][$k]['user']);
            }
        }
        return $this->responseSuccess($data);
    }




    /**
     * 获取dashboard-temperature map 数据
     *
    */
    public function temperatureMapData()
    {
        // 温度档位：<35, >=35 & <36, >=36 & <37.3, >=37.3 & <38, >=38 & < 39, >=39 & < 42, >=42

        // 查每个城市，每个温度档位的数量
        $range = [
            '1' => '`data` < 35',
            '2' => '`data` >= 35 and `data` < 36',
            '3' => '`data` >= 36 and `data` < 37.3',
            '4' => '`data` >= 37.3 and `data` < 38',
            '5' => '`data` >= 38 and `data` < 39',
            '6' => '`data` >= 39 and `data` < 42',
            '7' => '`data` >= 42'
        ];
        $range_case = 'case';
        foreach ($range as $k => $s) {
            $range_case .= ' when ' . $s . ' then ' . $k;
        }
        $range_case .= ' end temperature_range';
        $data = StpMonitorData::where('type', 'temperature')
            ->where('location', '<>', '')
            ->select('location', DB::raw($range_case), DB::raw('count(*) num'))
            ->groupBy('location')
            ->groupBy('temperature_range')
            ->get();
        // 再查每个城市每个温度档位的第一个数据的坐标作为该档温度在地图上展示的中心坐标
        $result = [];
        foreach ($data as $d) {
            if (in_array($d->temperature_range, ['1', 7])) {
                continue;
            }
            $coordinate = StpMonitorData::where('type', 'temperature')
                ->where('location', $d->location)
                ->whereRaw($range[$d->temperature_range])
                ->select(DB::raw('concat_ws(",",lat, lng) coordinate'))
                ->first()->toArray();
            $result[] = array_merge($d->toArray(), ['coordinate' => $coordinate['coordinate']]);
        }
        return $this->responseSuccess($result);
    }

    /**
     * 用户列表接口
     * 分页获取
     */
    public function usersRecord(Request $request)
    {
        $keyword= $request->input('keyword');
        $data = StpUser::when($keyword, function ($query, $keyword) {
            return $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%');
        })->orderBy('created_at', 'desc')->paginate(10)->toArray();
        foreach ($data['data'] as $k => $v) {
            unset($v['password']);
            if (empty($v['name'])) {
                $v['name'] = explode('@', $v['email'])[0];
            }
            $gender = StpUser::$genders[$v['gender']] ?? '';
            $gender = strtoupper(substr($gender, 0, 1)) . substr($gender, 1);
            $v['gender'] = $gender;
            $v['age'] = calcAge($v['birthday']);
            unset($v['birthday']);
            $v['abnormal'] = StpMonitorData::where('user_id', $v['id'])->where('data', '>', '37.2')->exists();
            $data['data'][$k] = $v;
        }
        return $this->responseSuccess($data);
    }



}
