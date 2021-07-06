<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{

    protected $table = 'global_setting';
    protected $casts = [
        'setting' => 'array',
    ];
    protected $guarded = [
        ' ',
    ];
}