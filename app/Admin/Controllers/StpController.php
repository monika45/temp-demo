<?php


namespace App\Admin\Controllers;


use App\Model\StpMonitorData;
use App\Model\StpUser;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Http\Request;

class StpController extends AdminController
{
    public function dashboard()
    {
        $data = [];
        // 今日注册的用户和总用户
        $data['total_user'] = StpUser::count();
        $data['today_user'] = StpUser::whereDate('created_at', '=', date('Y-m-d'))->count();
        // 今日新增的数据和总数据
        $data['total_data'] = StpMonitorData::count();
        $data['today_data'] = StpMonitorData::whereDate('created_at', '=', date('Y-m-d'))->count();
//        var_dump($data);die;
        return view('stp-backoffice.dashboard', $data);
    }

    public function userRecords()
    {
        return view('stp-backoffice.user-records');
    }

    public function userInfomation(Request $request)
    {
        $id = $request->input('id');
        if (empty($id)) {
            return redirect('/admin/');
        }
        $data = StpUser::formatUserInfo(StpUser::find($id));
        $token = StpUser::createToken($id);
        return view('stp-backoffice.user-information', ['user' => $data, 'token' => $token]);
    }

}
