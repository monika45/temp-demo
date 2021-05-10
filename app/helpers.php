<?php

function fullFileUrl($filePath)
{
    if (empty($filePath)) {
        return '';
    }
    return env('QINIU_DOMAIN') . '/' . ltrim($filePath, '/');
}

function arrMapToModel($model, $data)
{
    foreach ($data as $k => $v) {
        $model->{$k} = $v;
    }
    return $model;
}

function curlGet($url, $headers = [])
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$url, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $res = curl_exec($curl);
    return $res;
}

/**
 *
 * @param $data [k=>v,....]
 * @param $keystr {k}{k}
 *
 */
function getFormatValueFromData($data, $keystr): string
{
    if (empty($data) || empty($keystr)) {
        return '';
    }
    $str = '';
    $d = array_filter(explode('{', $keystr));
    foreach ($d as $v) {
        $v = rtrim($v, '}');
        $str .= $data[$v] ?? '';
    }
    return $str;
}

/**
 * 检查日期格式
 * @param $date 日期，正确格式应为：0000-00-00
 * @return bool true-日期格式正确 false-日期格式错误
*/
function checkDateFormat($date)
{
    $match = preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts);
    if (!$match || empty($parts)) {
        return false;
    }
    return checkdate($parts[2], $parts[3], $parts[1]);
}

/**
 * 检查时间格式
 * @param $time 时间，正确格式应为:00:00
 * @return bool true-时间格式正确 false-时间格式错误
*/
function checkTimeFormat($time)
{
    if (strlen($time) != 5) {
        return false;
    }
    $match = preg_match("/^([0-9]{2}):([0-9]{2})$/", $time, $parts);
    if (!$match || empty($parts)) {
        return false;
    }
    return true;
}

/**
 * 计算年龄
*/
function calcAge($birthday)
{
    if (empty($birthday)) {
        return '';
    }
    $diff = date('Y') - explode('-', $birthday)[0];
    if ($diff <= 0) {
        return '';
    }
    return $diff;

}
