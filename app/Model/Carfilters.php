<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Carfilters extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'car_id',
        'filter_tag',
        'filter_val'
    ];


    private static $filterTapMap = [
        'CarBrand' => 'brand',//getCarBrand获取车辆品牌;updateCarBrand设置车辆品牌
        'CarType' => 'cartype',
    ];



    public static function __callStatic($method, $parameters)
    {
        if (substr($method, 0, 3) === 'get') {
            $field = ltrim($method, 'get');
            $car_id = $parameters[0];
            if (array_key_exists($field, self::$filterTapMap)) {
                if (empty($car_id)) {
                    return '';
                }
                return self::where([
                    ['car_id', '=', $car_id],
                    ['filter_tag', '=', self::$filterTapMap[$field]]
                ])->value('filter_val');
            }
        }
        if (substr($method, 0, 6) === 'update') {
            $field = ltrim($method, 'update');
            $car_id = $parameters[0];
            $fieldVal = $parameters[1] ?? '';
            if (array_key_exists($field, self::$filterTapMap)) {
                if (empty($car_id)) {
                    return false;
                }
                self::updateOrCreate(
                    ['car_id' => $car_id, 'filter_tag' => self::$filterTapMap[$field]],
                    ['filter_val' => $fieldVal]
                );
                return true;
            }
        }
        return parent::__callStatic($method, $parameters);
    }




    /**
     * 获取车的标签
     * @param $car_id 车辆ID
     * @return array 标签数组
    */
    public static function getCarTags($car_id): array
    {
        if (empty($car_id)) {
            return [];
        }
        return self::where([
            ['car_id', '=', $car_id],
            ['filter_tag', '=', 'tag']
        ])->pluck('filter_val')->toArray();
    }

    /**
     * 更新车的标签
     * @param $car_id
     * @param $tags array|string 标签数组，如：['手动挡', '顶配车型']|'手动挡,顶配车型'
     * @return bool
     */
    public static function updateCarTags($car_id, $tags): bool
    {
        if (empty($car_id)) {
            return false;
        }
        self::where([
            ['car_id', '=', $car_id],
            ['filter_tag', '=', 'tag']
        ])->delete();
        if (empty($tags)) {
            return true;
        }
        if (!is_array($tags)) {
            $tags = array_filter(explode(',', $tags));
            if (empty($tags)) {
                return true;
            }
        }
        $datas = array_map(function($t) use ($car_id) {
            return [
                'car_id' => $car_id,
                'filter_tag' => 'tag',
                'filter_val' => $t
            ];
        }, $tags);
        DB::table('carfilters')->insert($datas);
        return true;
    }
}
