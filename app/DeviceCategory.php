<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceCategory extends Model
{
    protected $table = 'device_categories';

    protected $guarded = [
        'id',
    ];
    
    public function device()
    {
        return $this->hasOne(Device::class);
    }
}
