<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $table = 'business_settings';
     protected $fillable = [
        'key',
        'value'
    ];
}
