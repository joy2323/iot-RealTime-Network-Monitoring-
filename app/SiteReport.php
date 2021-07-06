<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteReport extends Model
{
    //

    protected $table = 'site_reports';

    protected $guarded = [
    ];
    public function site()
    {
      return $this->belongsTo(Site::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // public function getDownSites($site_id)
    // {
    //     $this->site_id = $site_id;
    //     $getSiteDown = SiteReport::where('site_id',$this->site_id)->where('status','Active')->where('alarm_name','SITE DOWN')->orderBy('id','desc')->first();
    //         if(size($getSiteDown)>0){
    //             return $getSiteDown->alarm_name;
    //         }else{
    //             return '';
    //         }
    // }
}
