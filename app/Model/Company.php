<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function cars()
    {
        return $this->hasMany('App\Model\Car');
    }
}
