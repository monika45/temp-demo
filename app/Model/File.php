<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'file';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $attributes = [
        'cdnSynchronized' => 0,
        'usedCount' => 1,
        'clientHash' => '',
        'isPrivate' => 0,
        'driver' => 'qiniu'
    ];

    public function insertGetIdWithType($data, $type)
    {
        $data['usedTo'] = $type;
        arrMapToModel($this, $data);
        $this->save();
        return $this->id;
    }
}
