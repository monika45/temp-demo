<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QiniuService;

class FileController extends Controller
{
    /**
     * 获取七牛上传参数
    */
    public function getQiniuParams(Request $request)
    {
        $ext = $request->input('ext', '');
        $qiniu = new QiniuService();
        $data = $qiniu->getUploadParams($ext);
        return $this->responseSuccess($data);
    }
}
