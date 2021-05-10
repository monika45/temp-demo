<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'baseinfo.vin' => 'bail|required',
            'baseinfo.deliveryTime' => 'required',
            'baseinfo.registrationTime' => 'required',
            'baseinfo.mileage' => 'required',
            'baseinfo.emissionStdText' => 'required',
            'baseinfo.displacementML' => 'required',
            'baseinfo.gears' => 'required',
            'baseinfo.licensePlaceCode' => 'required',
            'baseinfo.licensePlaceName' => 'required',
            'statusDesc.statusDesc' => 'required',
            'statusDesc.price' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'baseinfo.vin.required' => 'VIN必填',
            'baseinfo.deliveryTime.required' => '车辆出厂时间必填',
            'baseinfo.registrationTime.required' => '车辆上牌时间必填',
            'baseinfo.mileage.required' => '表显里程必填',
            'baseinfo.emissionStdText.required' => '排放标准必填',
            'baseinfo.displacementML.required' => '排量必填',
            'baseinfo.gears.required' => '挡位必填',
            'baseinfo.forseats.required' => '座位数必填',
            'baseinfo.licensePlaceCode.required' => '牌照归属地必填',
            'baseinfo.licensePlaceName.required' => '牌照归属地必填',
            'statusDesc.statusDesc.required' => '车况描述必填',
            'statusDesc.price.required' => '预估售价必填',
        ];
    }
}
