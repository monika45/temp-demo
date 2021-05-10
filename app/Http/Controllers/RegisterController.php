<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController as Register;

class RegisterController extends Register
{

    public function index(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];
        return $this->create($data);
    }

    protected function create(array $data)
    {
        return parent::create($data);
    }


}
