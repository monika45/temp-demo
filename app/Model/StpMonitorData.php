<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StpMonitorData extends Model
{
    protected $table = 'stp_monitor_datas';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'day',
        'time',
        'type',
        'variation',
        'data',
        'created_at',
        'location',
        'lat',
        'lng'
    ];

    public static $dataUnits = [
        'temperature' => '℃',
        'pulse' => 'bt/min'
    ];

    /**
     * 获取数据变化值，
     * 跟最近一次该用户提交的数据对比
     * @param $user_id 用户ID
     * @param $data 本次测量值
     * @param $type 数据类型：temperature/pulse
     * @return false|string
     */
    public static function getVariation($user_id, $type, $data, $day, $time)
    {
        if (empty($user_id) || empty($data) || empty($type)) {
            return false;
        }
        $lastData = self::where([
            ['user_id', '=', $user_id],
            ['type', '=', $type]
        ])
            ->whereRaw('concat(`day`, `time`) < ?', [$day . $time])
            ->orderBy('day', 'desc')
            ->orderBy('time', 'desc')
            ->value('data');
        if (empty($lastData)) {
            return '';
        }
        return self::calVariation($data, $lastData);
    }

    /**
     * 计算变化值
     * @param $cur_data 当前值
     * @param $last_data 上一个值
     * @return string
     */
    public static function calVariation($cur_data, $last_data)
    {
        $variation = round((double)$cur_data - (double)$last_data, 1);
        if (empty($variation)) {
            return '';
        }
        if ($variation > 0) {
            return '+' . $variation;
        }
        return (string) $variation;
    }

    public function user()
    {
        return $this->belongsTo(StpUser::class);
    }
}
