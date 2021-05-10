<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Carimg extends Model
{
    public $timestamps = false;

    protected $attributes = [
        'img_desc' => ''
    ];

    public function setImgDescAttribute($value)
    {
        $this->attributes['img_desc'] = $value ?? '';
    }

    public function file()
    {
        return $this->belongsTo('App\Model\File');
    }
}
