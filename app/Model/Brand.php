<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{

    public $timestamps = false;

    protected $fillable = ['name'];

    public static function updateBrand($brand_name)
    {
        if (empty($brand_name)) {
            return false;
        }
        return self::firstOrCreate(['name' => $brand_name]);
    }

    public static function getBrandNames()
    {
        return self::pluck('name');
    }
}
