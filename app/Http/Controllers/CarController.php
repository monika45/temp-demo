<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Model\Area;
use App\Model\Brand;
use App\Model\Car;
use App\Model\Carfilters;
use App\Model\Carimg;
use App\Model\CarimgGroup;
use App\Model\Carinfo;
use App\Model\Cartag;
use App\Model\Draft;
use App\Model\File;
use App\Model\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    /**
     * 车辆列表
    */
    public function index(Request $request)
    {
        $company_id = $request->input('company_id');
        $brand_names = $request->input('brand_names');//品牌名,多选，多个逗号隔开
        $car_types = $request->input('car_types');//车辆形式,多选，多个逗号隔开
        $license_place_codes = $request->input('license_place_codes');//拍照归属地,多选，多个逗号隔开
        $emission_stds = $request->input('emission_stds');//排放标准,多选，多个逗号隔开
        $tags = $request->input('tags');//标签、热门类型,多选，多个逗号隔开
        $forseats = $request->input('forseats');//座位数,多选，多个逗号隔开
        $sort = $request->input('sort');//字段|排序规则，如：delivery_time|desc（车龄最短）, estimated_price|asc（价格最低）

        if (empty($company_id)) {
            //根据当前登录的用户获取企业ID
            $company_id = $this->authUserCompanyId();
            if (empty($company_id)) {
                return $this->responseError('获取企业信息失败');
            }
        }

        $filters = null;
        if ($brand_names || $car_types || $tags) {
            $filters = Carfilters::where(function($query) use ($brand_names, $car_types, $tags) {
                if ($brand_names) {
                    $query->where('filter_tag', 'brand')
                        ->whereIn('filter_val', explode(',', $brand_names));
                }
                if ($car_types) {
                    $query->where('filter_tag', 'cartype')
                        ->whereIn('filter_val', explode(',', $car_types));
                }
                if ($tags) {
                    $query->where('filter_tag', 'tag')
                        ->whereIn('filter_val', explode(',', $tags));
                }
            })
            ->select('car_id')
            ->groupBy('car_id');
        }


        $datas = Car::where('company_id', $company_id)
            ->where(function ($query) use ($license_place_codes, $emission_stds, $forseats) {
                if ($license_place_codes) {
                    $query->whereIn('license_place_code', explode(',', $license_place_codes));
                }
                if ($emission_stds) {
                    $query->whereIn('emission_std', explode(',', $emission_stds));
                }
                if ($forseats) {
                    $forseats = explode(',', $forseats);
                    $query->where(function ($query) use ($forseats) {
                        $index = array_search(6, $forseats);
                        if ($index !== false) {
                            unset($forseats[$index]);
                            if (count($forseats)) {
                                $query->whereIn('forseats', $forseats)
                                    ->orWhere('forseats', '>=', 6);
                            } else {
                                $query->whereIn('forseats', $forseats);
                            }
                        } else {
                            $query->whereIn('forseats', $forseats);
                        }
                    });
                }
            })
            ->select('id', 'name', 'mileage', 'delivery_time')
            ->when($sort, function ($query, $sort) {
                $sort = explode('|', $sort);
                return $query->orderBy($sort[0], $sort[1]);
            }, function ($query) {
                return $query->orderBy('created_at', 'desc');
            })
            ->when($filters, function ($query, $filters) {
                return $query->joinSub($filters, 'filters', function($join) {
                    $join->on('cars.id', '=', 'filters.car_id');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $result = [];
        foreach ($datas as $k => $d) {
            $img = Carimg::where('car_id', $d->id)->first();
            if ($img) {
                $img = fullFileUrl($img->file->filePath);
            }
            $result[] = [
                'id' => $d->id,
                'name' => $d->name,
                'mileage' => $d->mileage,
                'delivery_time' => $d->delivery_time,
                'thumb' => $img ?? ''
            ];
        }
        return $this->responseSuccess($result);
    }


    /**
     * 获取列表页面筛选数据
    */
    public function filters()
    {
        // 品牌
        $brands = Brand::all();
        // 牌照归属地
        $places = Area::getProvinces();
        // 热门类型
        $tags = Cartag::all();
        $datas = [
            'brands' => $brands,
            'places' => $places,
            'tags' => $tags
        ];
        return $this->responseSuccess($datas);
    }

    /**
     * 获取车辆信息
     * @param Request $request
     * @param $id
     * @return 车辆详情
     */
    public function show(Request $request, $id)
    {
        $field = $request->input('field');
        $selectFields = '*';
        $tags = [];
        if (!empty($field)) {
            $selectFields = explode(',', $field);
            $tagsIndex = array_search('tags', $selectFields);
            if ($tagsIndex !== false) {
                //查标签
                unset($selectFields[$tagsIndex]);
                $tags = Carfilters::getCarTags($id);
            }
        }
        $data = Car::where('id', $id)->select($selectFields)->first()->toArray();
        if ($tags) {
            $data['tags'] = $tags;
        }
        return $this->responseSuccess($data);
    }


    /**
     * 获取图片分组
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function imgGroups(Request $request)
    {
        $car_id = $request->input('car_id', 0);
        //获取图片分组
        $groups = CarimgGroup::all()->toArray();
        $groupids = array_flip(array_column($groups, 'id'));
        // 有car_id 获取已上传的对应图片
        if ($car_id) {
            $imgs = Carimg::where('car_id', $car_id)
                ->whereIn('group_id', array_keys($groupids))->get();
            foreach ($imgs as $img) {
                $groups[$groupids[$img->group_id]]['imgs'][] = [
                    'id' => $img->id,
                    'imgdesc' => $img->img_desc,
                    'imgsrc' => fullFileUrl($img->file->filePath)
                ];
            }
        }
        return $this->responseSuccess($groups);
    }

    /**
     * 获取维保记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function maintanenceRecords(Request $request)
    {
        $car_id = $request->input('car_id', 0);
        $need_carinfo = $request->input('need_carinfo', 'yes');
        if (empty($car_id)) {
            return $this->responseError('获取维保记录失败：carId缺失');
        }
        $records = MaintenanceRecord::where('car_id', $car_id)->get();
        $maintenance_res = [];
        foreach ($records as $record) {
            $maintenance_res[] = [
                'id' => $record->id,
                'originName' => $record->file->fileOriginName,
                'url' => fullFileUrl($record->file->filePath)
            ];
        }
        if ($need_carinfo == 'yes') {
            $car = Car::where('id', $car_id)->select('status_desc', 'estimated_price')->first();
            return $this->responseSuccess([
                'status_desc' => $car->status_desc,
                'estimated_price' => $car->estimated_price,
                'maintenance_records' => $maintenance_res
            ]);
        }
        return $this->responseSuccess($maintenance_res);

    }


    /**
     *  编辑车辆信息
     * @param StoreCarRequest $request
     */
    public function saveCar(StoreCarRequest $request)
    {

        $baseinfo = $request->input('baseinfo');
        $imgGroups = $request->input('imgGroups');
        $statusDesc = $request->input('statusDesc');
        $draft_id = $request->input('draftId');
        $authUser = $this->authUser();
        $company_id = $authUser->company->id;


        DB::beginTransaction();
        try {
            // 车辆信息
            if (empty($baseinfo['carId'])) {
                $mCar = new Car();
            } else {
                $mCar = Car::find($baseinfo['carId']);
            }
            $mCar->vin_code = $baseinfo['vin'];
            $mCar->name = $baseinfo['name'];
            $mCar->delivery_time = $baseinfo['deliveryTime'];
            $mCar->registration_time = $baseinfo['registrationTime'];
            $mCar->mileage = $baseinfo['mileage'];
            $mCar->emission_std = $baseinfo['emissionStdText'];
            $mCar->displacement_ml = $baseinfo['displacementML'];
            $mCar->gears = $baseinfo['gears'];
            $mCar->forseats = $baseinfo['forseats'];
            $mCar->license_place_code = $baseinfo['licensePlaceCode'];
            $mCar->license_place_name = $baseinfo['licensePlaceName'];
            $mCar->company_id = $company_id;
            $mCar->status_desc = $statusDesc['statusDesc'];
            $mCar->estimated_price = $statusDesc['price'];
            $mCar->save();
            //标签
            Carfilters::updateCarTags($mCar->id, $baseinfo['tags']);
            // 根据vin查品牌和车辆形式
            $carinfo = Carinfo::getDetailInfo($baseinfo['vin'], ['brand_name', 'car_type']);
            // 品牌
            Carfilters::updateCarBrand($mCar->id, $carinfo['brand_name']);
            //车辆形式
            Carfilters::updateCarType($mCar->id, $carinfo['car_type']);

            // 图片
            foreach ($imgGroups as $imgGroup) {
                foreach ($imgGroup['imgs'] as $img) {
                    if (empty($img['id'])) {
                        $mFile = new File();
                        $mCarimg = new Carimg();
                        $mCarimg->file_id = $mFile->insertGetIdWithType([
                            'hash' => $img['fileInfo']['hash'],
                            'fileOriginName' => $img['fileInfo']['originName'],
                            'fileName' => $img['fileInfo']['fileName'],
                            'filePath' => $img['fileInfo']['filePath'],
                            'mime' => $img['fileInfo']['mime'],
                            'ext' => $img['fileInfo']['ext'],
                            'size' => $img['fileInfo']['size']
                        ], 'car');
                    } else {
                        $mCarimg = Carimg::find($img['id']);
                    }
                    $mCarimg->car_id = $mCar->id;
                    $mCarimg->group_id = $imgGroup['id'];
                    $mCarimg->img_desc = $img['imgdesc'];
                    $mCarimg->save();
                }
            }
            // 添加新增的维护记录文件
            foreach ($statusDesc['files'] as $record) {
                if (empty($record['id'])) {
                    $mRecordFile = new File();
                    $mMaintenanceRecord = new MaintenanceRecord();
                    $mMaintenanceRecord->file_id = $mRecordFile->insertGetIdWithType([
                        'hash' => $record['fileInfo']['hash'],
                        'fileOriginName' => $record['fileInfo']['originName'],
                        'fileName' => $record['fileInfo']['fileName'],
                        'filePath' => $record['fileInfo']['filePath'],
                        'mime' => $record['fileInfo']['mime'],
                        'ext' => $record['fileInfo']['ext'],
                        'size' => $record['fileInfo']['size']
                    ], 'maintenance_record');
                    $mMaintenanceRecord->car_id = $mCar->id;
                    $mMaintenanceRecord->save();
                }
            }
            // 如果有草稿ID删除草稿
            if (!empty($draft_id)) {
                Draft::destroy($draft_id);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('err:'.$e->getMessage());
        }
        return $this->responseSuccess(['car_id' => $mCar->id]);
    }

    /**
     * 删除图片
    */
    public function delImg(Request $request)
    {
        $id = $request->input('id');
        if (empty($id)) {
            return $this->responseError('ID缺失');
        }
        if (Carimg::destroy($id)) {
            return $this->responseSuccess('删除成功');
        }
        return $this->responseError('删除失败');
    }

    /**
     * 删除维护记录
    */
    public function delMaintenanceRecord(Request $request)
    {
        $id = $request->input('id');
        if (empty($id)) {
            return $this->responseError('ID缺失');
        }
        if (MaintenanceRecord::destroy($id)) {
            return $this->responseSuccess('删除成功');
        }
        return $this->responseError('删除失败');

    }

    /**
     * 获取车辆配置信息
    */
    public function carSpec(Request $request)
    {
        $vin = $request->input('vin');
        $car_id = $request->input('car_id');

        if (empty($vin) && empty($car_id)) {
            return $this->responseError('参数缺失');
        }
        if ($vin) {
            $detail_info = Carinfo::where('vin_code', $vin)->value('detail_info');
        } elseif ($car_id) {
            $car = Car::find($car_id);
            $detail_info = empty($car->spec) ? [] : $car->spec->detail_info;
        }

        //根据key匹配配置值
        $specs = Carinfo::getSpecs();
        $indexes = [];
        foreach ($specs as $k1 => $v1) {
            foreach ($v1['child'] as $k2 => $v2) {
                if (array_key_exists($v2['name'], Carinfo::$spec_key_map)) {
                    $indexes[$v2['name']] = $k1 . '_' . $k2;
                }
            }
        }
        foreach (Carinfo::$spec_key_map as $spec_key => $data_key) {
            $index = explode('_', $indexes[$spec_key]);
            $specs[$index[0]]['child'][$index[1]]['value'] = getFormatValueFromData($detail_info, $data_key);
        }
        return $this->responseSuccess($specs);
    }




}
