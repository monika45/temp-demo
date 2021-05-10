<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{

    protected $attributes = [
        'mark_id' => 0
    ];

    protected $casts = [
        'content' => 'array'
    ];

    public function setMarkIdAttribute($value)
    {
        $this->attributes['mark_id'] = $value ?? 0;
    }
}
