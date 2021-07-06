<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    protected $guarded = [
        'id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function site()
    {
        return $this->hasOne(Site::class, 'site_id');
    }


    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function siteReport()
    {
        return $this->belongsTo(SiteReport::class);
    }

    public function category()
    {
        return $this->belongsTo(DeviceCategory::class, 'device_category_id');
    }
}
