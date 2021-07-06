<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = 'details';

    protected $guarded = [
        'id',
    ];
    

    public function siteStatus()
    {
        return $this->hasMany(SiteStatus::class);
    }
}
