<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Carinfo extends Model
{
    public $timestamps = false;

    protected $casts = [
        'detail_info' => 'array'
    ];

    //映射VIN查询接口返回的参数key和系统预定义的参数key
    public static $spec_key_map = [
        'year' => '{year}',//年款
        'cylinderNum' => '{cylinder_number}',//发动机缸数
        'remark' => '{remark}',//备注
        'transmission' => '{transmission_type}', //变速箱简称
        'transmissionType' => '{transmission_type}',//变速器类型
        'carType' => '{car_type}',//车辆类型
        'airBag' => '{air_bag}',//安全气囊
        'producer' => '{manufacturer}',//厂家名称
        'doors' => '{door_num}',//车门数
        'carName' => '{brand_name}{car_line}{year}款{sale_name}',//车型名称
        'fuelLabel' => '{fuel_num}',//燃油标号
        'jetType' => '{jet_type}',//发动机喷射类型
        'guidingPrice' => '{guiding_price}',//指导价格（万元）
        'assemblyFactory' => '{assembly_factory}',//组装厂
        'marketTime' => '{made_year}年{made_month}月',//生产年份 上市月份
        'vin' => '{vin}',//
        'driveWay' => '{drive_style}',//驱动方式
        'TotalWeight' => '{car_weight}',//装备质量(KG)
        'cylinderArrangementForm' => '{cylinder_form}',//汽缸形式
        'gearsNum' => '{gears_num}',//档位数
        'fuel' => '{fuel_Type}',//燃油类型
        'displacementL' => '{output_volume}',//排量(L)
        'environmentalStd' => '{effluent_standard}',//排放标准
        'maxPower' => '{power}',//功率/转速(Kw/R)
        'seats' => '{seat_num}',//座位数
        'classification' => '{vehicle_level}',//车辆级别
        'stopYear' => '{stop_year}',//停产年份
        'engine' => '{engine_type}',//发动机型号
        'engineModel' => '{engine_type}',//发动机型号
        'seatingCapacity' => '{car_body}',//车身形式
        'bodyStructure' => '{car_body}'
    ];

    public static $detail_fields_map = [
        // 系统标识key => 接口返回、carinfos表存储的实际key
        'brand_name' => 'brand_name',
        'car_type' => 'car_type'
    ];

    public static $specs = [];

    public static function getSpecs()
    {
        if (!empty(self::$specs)) {
            return self::$specs;
        }
        $data = file_get_contents(storage_path('app') . '/spec.json');
        self::$specs = json_decode($data, true);
        return self::$specs;
    }


    /**
     * 根据VIN获取需要的信息
     * @param $vin
     * @param $fields ['brand_name', 'car_type'] 系统预定义的key，需要根据$detail_fields_map转换成接口实际返回（数据库实际存储）的字段
     * @return array
     */
    public static function getDetailInfo($vin, $fields): array
    {
        if (empty($fields)) {
            return [];
        }
        $data = self::where('vin_code', $vin)->value('detail_info');
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = $data[self::$detail_fields_map[$field]] ?? '';
        }
        return $result;
    }

}
