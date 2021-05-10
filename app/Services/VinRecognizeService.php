<?php


namespace App\Services;



use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;



class VinRecognizeService
{

    private static $accessKeyId;
    private static $accessKeySecret;

    public static function setAccessKey($accessKeyId, $accessKeySecret)
    {
        self::$accessKeyId = $accessKeyId;
        self::$accessKeySecret = $accessKeySecret;
    }

    public static function handle($imgurl)
    {
        $vinResult = [
            'vinCode' => '',
            'errMsg' => ''
        ];
        AlibabaCloud::accessKeyClient(self::$accessKeyId, self::$accessKeySecret)
            ->regionId('cn-shanghai')
            ->asDefaultClient();

        try {
            $result = AlibabaCloud::ocr()
                ->v20191230()
                ->recognizeVINCode()
                ->withImageURL($imgurl)
                ->request();
            $vinResult['vinCode'] = $result->toArray()['Data']['VinCode'];
//            print_r($result->toArray());
        } catch (ClientException $e) {
//            print_r($e->getErrorMessage());
            $vinResult['errMsg'] = $e->getErrorMessage();
        } catch (ServerException $e) {
//            print_r($e->getErrorMessage());
            $vinResult['errMsg'] = $e->getErrorMessage();

        }
        return $vinResult;

    }


}
