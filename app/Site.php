<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
   protected $guarded = [
        'id',
    ]; 

    public function siteReport()
    {
        return $this->hasMany(SiteReport::class);
    }

    public function devices()
    {
        return $this->belongsTo(Device::class );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function location()
    {
        return $this->hasOne(Location::class,'serial_number','serial_number');
    }

   
}