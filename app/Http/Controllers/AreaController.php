<?php


namespace App\Http\Controllers;


use App\Model\Area;

class AreaController extends Controller
{

    public function provinces()
    {
        $data = Area::getProvinces();
        return $this->responseSuccess($data);
    }

}
