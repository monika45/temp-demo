<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    public static $provinces = [];
    public static function getProvinces(): array
    {
        if (self::$provinces) {
            return self::$provinces;
        }
        $data = file_get_contents(storage_path('app') . '/province.json');
        self::$provinces = json_decode($data, true);
        return self::$provinces;
    }
}
