<?php

namespace App\Admin\Actions\StpUser;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ShowDatas extends RowAction
{
    public $name = 'Monitor Data';

//    public function handle(Model $model)
//    {
//        // $model ...
//
//        return $this->response()->success('Success message.')->refresh();
//    }

    public function href()
    {
        return '/admin/stp-user-monitor-datas/' . $this->getKey();
    }

}
