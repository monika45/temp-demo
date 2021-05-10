<?php

namespace App\Http\Controllers;

use App\Model\Cartag;
use Illuminate\Http\Request;

class CartagController extends Controller
{
    /**
     *
     * 车辆标签
    */
    public function index()
    {
        $data = Cartag::pluck('name');
        return $this->responseSuccess($data);
    }
}
