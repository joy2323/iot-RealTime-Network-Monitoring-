<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteStatus extends Model
{
    protected $table = 'site_status';

    protected $guarded = [
    ];

    public function site()
    {
        return $this->hasOne(Site::class,'serial_number');
    }



}
