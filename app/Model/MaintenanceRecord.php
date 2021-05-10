<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    public $timestamps = false;

    public function file()
    {
        return $this->belongsTo('App\Model\File');
    }
}
