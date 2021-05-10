<?php


namespace App\Services;


use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class QiniuService
{
    private $accessKey;
    private $secretKey;
    private $bucket;

    public function __construct($accessKey = null, $secretKey = null, $bucket = null)
    {
        $this->accessKey = $accessKey ?? env('QINIU_AK');
        $this->secretKey = $secretKey ?? env('QINIU_SK');
        $this->bucket = $bucket ?? env('QINIU_BUCKET');
    }

    public function getUploadParams($ext)
    {
        $res = [
            'key' => date('Y') . '/' . date('md') . '/' . md5(uniqid(microtime(true), true))
        ];
        if ($ext) {
            $res['key'] .= '.' . $ext;
        }
        $auth = new Auth($this->accessKey, $this->secretKey);
        $res['token'] = $auth->uploadToken($this->bucket);
        return $res;
    }

}
