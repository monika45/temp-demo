<?php

namespace App\Http\Controllers;

use App\Model\Brand;
use App\Model\Carinfo;
use App\Services\VinRecognizeService;
use Illuminate\Http\Request;
use AlibabaCloud\SDK\ViapiUtils\ViapiUtils;
use Illuminate\Support\Str;

class VinIdentifyController extends Controller
{
    /**
     * VIN码识别
     * @param vinimg file|base64_str
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //获取上传的文件
        $file = $request->file('vinimg');
        $accessKeyId = env('ALI_OCR_ACCESSKEYID');
        $accessKeySecret = env('ALI_OCR_ACCESSKEYSECRET');
        if (empty($accessKeyId) || empty($accessKeySecret)) {
            return $this->responseError('阿里SDK参数缺失');
        }
        $distDir = storage_path('tmp');
        if (empty($file)) {
            //通过base64获取
            $base64 = $request->get('vinimg');
            if (empty($base64)) {
                return $this->responseError('请上传文件');
            }
            $filename = 'vinimg' . Str::random(8) . '.png';
            file_put_contents($distDir . '/' . $filename, base64_decode(explode(',', $base64)[1]));
        } else {
            $ext = $file->getClientOriginalExtension();
            $filename = 'vinimg' . Str::random(8) . '.' . $ext;
            $file->move($distDir, $filename);
        }
        $fileUrl = $distDir .'/' . $filename;
        //调SDK生成URL
        $fileLoadAddress = ViapiUtils::upload($accessKeyId, $accessKeySecret, $fileUrl);
        unlink($fileUrl);
        //调SDK识别
        VinRecognizeService::setAccessKey($accessKeyId, $accessKeySecret);
        $vinResult = VinRecognizeService::handle($fileLoadAddress);
        //返回识别结果：vin码
        return $this->responseSuccess($vinResult);
    }

    /**
     * VIN码查询：
     * 调接口查询，获取到后存储到数据库中，下次查询直接从数据库查询
    */
    public function vinQuery(Request $request)
    {
        $vin_code = $request->input('vin_code');
        if (empty($vin_code)) {
            return $this->responseError('vin码缺失');
        }

        $mCarinfo = Carinfo::where('vin_code', $vin_code)->first();
        if (!empty($mCarinfo)) {
            $pageData = [
                'name' => $mCarinfo->detail_info['brand_name'] . $mCarinfo->detail_info['car_line'] . $mCarinfo->detail_info['year'] . ($mCarinfo->detail_info['year'] ? '款' : '') . $mCarinfo->detail_info['sale_name'],
                'replaceData' => [
                    'displacementML' => $mCarinfo->detail_info['output_volume'],
                    'gears' => $mCarinfo->detail_info['gears_num'],
                    'forseats' => $mCarinfo->detail_info['seat_num'],
                ]
            ];
            return $this->responseSuccess($pageData);
        }

        $api_url = env('VIN_QUERY_API') . '?vin=' . $vin_code;
        $headers = [
            'Authorization:APPCODE ' . env('VIN_QUERY_APPCODE')
        ];
        $res = json_decode(curlGet($api_url, $headers), true);
        if ($res['code'] != 200) {
            return $this->responseError('VIN查询失败:' . $res['msg']);
        }
        //获取数据存数据库
        $data = $res['data']['data'];
        if (isset($data['vin'])) {
            $data['vin'] = strtoupper($data['vin']);
        }
        $pageData = [
            'name' => $data['brand_name'] . $data['car_line'] . $data['year'] . ($data['year'] ? '款' : '') . $data['sale_name'],
            'replaceData' => [
                'displacementML' => $data['output_volume'],
                'gears' => $data['gears_num'],
                'forseats' => $data['seat_num'],
            ]
        ];
        $mCarinfo = new Carinfo();
        $mCarinfo->vin_code = $vin_code;
        $mCarinfo->detail_info = $data;
        $mCarinfo->save();
        Brand::updateBrand($data['brand_name']);

        return $this->responseSuccess($pageData);

    }
}
