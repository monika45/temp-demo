<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value ?? '';
    }

    public function company()
    {
        return $this->belongsTo('App\Model\Company');
    }

    public function carimgGroups()
    {
        return $this->hasMany('App\Model\CarimgGroup');
    }

    public function carimgs()
    {
        return $this->hasMany('App\Model\Carimg');
    }

    public function spec()
    {
        return $this->hasOne('App\Model\Carinfo', 'vin_code', 'vin_code');
    }


}
